<?php
/**
 * Jenbury Financial - Admin Add Lesson Content Page
 */
$this->assign('title', 'Add Lesson Content');
$this->Html->css(['admin/_add-content'], ['block' => true]); // Added CSS link
?>

<div class="add-content-page admin-add-page"> <?php // Added page wrapper class ?>

    <header class="admin-page-header">
        <h1>Add New Lesson to: <?= h($module->title) ?></h1>
        <div class="admin-back-link">
            <?= $this->Html->link('‹ Back to Manage Contents',
                ['action' => 'manageContents', $module->id],
                ['class' => 'button', 'escape' => false]
            ) ?>
        </div>
    </header>

    <div class="admin-form-container admin-content-card"> <?php // Standardized container ?>
        <div class="admin-card-body"> <?php // Standardized body ?>
            <?= $this->Form->create($content, ['type' => 'post', 'id' => 'addContentForm', 'novalidate' => true]) ?>
            <?= $this->Form->hidden('module_id', ['value' => $module->id]) ?>
            <fieldset>
                <legend><?= __('Lesson Details') ?></legend> <?php // Added legend ?>
                <?= $this->Form->control('title', [
                    'label' => 'Title',
        'required' => true
      ]) ?>

      <?= $this->Form->control('content', [
        'type' => 'textarea',
        'label' => 'Lesson Content (HTML)',
        'class' => 'ckeditor-editor',
        'rows' => 20,
        'id' => 'editor',
        'escape' => false
      ]) ?>

      <?= $this->Form->control('is_active', [
        'type' => 'checkbox',
        'label' => 'Make this content active'
      ]) ?>
    </fieldset>

    <div class="form-actions step-actions"> <?php // Standardized button wrapper ?>
      <?= $this->Form->button(__('Save Lesson Content'), ['class' => 'button button-primary']) ?>
      <?= $this->Html->link(__('Cancel'), ['action' => 'manageContents', $module->id], ['class' => 'button button-secondary']) ?>
    </div>
    <?= $this->Form->end() ?>
        </div> <?php // Close admin-card-body ?>
    </div> <?php // Close admin-form-container ?>
</div> <?php // Close add-content-page ?>

<!-- Load TinyMCE -->
<script src="https://cdn.tiny.cloud/1/jos8yto3u1996z25enmnvu067w84cqpnhl53gnhs1ggypmu4/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    tinymce.init({
      selector: '#editor',
      plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount help autoresize fullscreen code',
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code fullscreen help',
      // images_upload_url: '/admin/uploads/tinymce_upload', // Replaced by images_upload_handler
      // automatic_uploads: true, // Handled by images_upload_handler
      file_picker_types: 'image media file',
      images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
        var xhr, formData;
        var csrfTokenEl = document.querySelector('meta[name="csrfToken"]');
        if (!csrfTokenEl) {
            console.error('CSRF meta tag not found for images_upload_handler!');
            reject({ message: 'CSRF token not found. Cannot upload image.', remove: true });
            return;
        }
        var csrfToken = csrfTokenEl.getAttribute('content');

        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '<?= $this->Url->build('/admin/uploads/tinymce_upload') ?>');
        
        xhr.setRequestHeader('X-CSRF-Token', csrfToken);

        xhr.upload.onprogress = (e) => {
          progress(e.loaded / e.total * 100);
        };

        xhr.onload = () => {
          var json;

          if (xhr.status === 403) {
            reject({ message: 'HTTP Error: ' + xhr.status + '. CSRF token validation failed.', remove: true });
            return;
          }

          if (xhr.status < 200 || xhr.status >= 300) {
            reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
            return;
          }

          try {
            json = JSON.parse(xhr.responseText);
          } catch (e) {
            reject({ message: 'Invalid JSON: ' + xhr.responseText, remove: true });
            return;
          }

          if (!json || typeof json.location != 'string') {
            reject({ message: 'Invalid JSON: ' + xhr.responseText, remove: true });
            return;
          }

          resolve(json.location);
        };

        xhr.onerror = () => {
          reject({ message: 'Image upload failed due to a XHR Transport error. Code: ' + xhr.status, remove: true });
        };

        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());

        xhr.send(formData);
      }),
      file_picker_callback: function (cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        
        if (meta.filetype === 'image') {
          input.setAttribute('accept', 'image/*');
        } else if (meta.filetype === 'media') {
          input.setAttribute('accept', 'video/*,audio/*');
        } else { // 'file' for PDF, Excel, Word etc.
          input.setAttribute('accept', '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx');
        }

        input.onchange = function () {
          var file = this.files[0];
          
          if (meta.filetype === 'file' || meta.filetype === 'media') {
            console.log('File picker callback triggered for type:', meta.filetype, 'File:', file.name);
            var xhr, formData;
            var csrfTokenEl = document.querySelector('meta[name="csrfToken"]');
            if (!csrfTokenEl) {
                console.error('CSRF meta tag not found for file_picker_callback!');
                tinymce.activeEditor.windowManager.alert('CSRF token not found. Cannot upload file.');
                return;
            }
            var csrfToken = csrfTokenEl.getAttribute('content');
            console.log('CSRF Token for file_picker_callback:', csrfToken);

            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '<?= $this->Url->build('/admin/uploads/tinymce_upload') ?>');
            xhr.setRequestHeader('X-CSRF-Token', csrfToken);

            xhr.onload = function() {
              console.log('Upload XHR onload triggered. Status:', xhr.status, 'Response:', xhr.responseText);
              var json;
              if (xhr.status === 403) {
                tinymce.activeEditor.windowManager.alert('HTTP Error: ' + xhr.status + '. CSRF token validation failed.');
                return;
              }
              if (xhr.status < 200 || xhr.status >= 300) {
                tinymce.activeEditor.windowManager.alert('HTTP Error: ' + xhr.status + '. Response: ' + xhr.responseText);
                return;
              }
              try {
                json = JSON.parse(xhr.responseText);
              } catch (e) {
                console.error('Invalid JSON in file_picker_callback. Response:', xhr.responseText, 'Error:', e);
                tinymce.activeEditor.windowManager.alert('Invalid JSON: ' + xhr.responseText);
                return;
              }
              if (!json || typeof json.location != 'string') {
                console.error('Invalid JSON or no location key in file_picker_callback. JSON:', json);
                tinymce.activeEditor.windowManager.alert('Invalid JSON: No location key found. Response: ' + xhr.responseText);
                return;
              }
              console.log('Upload successful for file_picker_callback. Location:', json.location);
              cb(json.location, { title: file.name, text: file.name });
            };
            xhr.onerror = function () {
              console.error('Upload XHR onerror triggered in file_picker_callback. Status:', xhr.status);
              tinymce.activeEditor.windowManager.alert('Upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };
            formData = new FormData();
            formData.append('file', file, file.name);
            console.log('Sending XHR request for file upload via file_picker_callback...');
            xhr.send(formData);
          } else if (meta.filetype === 'image') {
            // This path in file_picker_callback for images is usually a fallback 
            // if images_upload_handler is not used or if a generic file picker is used for images.
            console.log('File picker callback for image type (using blob URI):', file.name);
            var reader = new FileReader();
            reader.onload = function () {
              var id = 'blobid' + (new Date()).getTime();
              var blobCache = tinymce.activeEditor.editorUpload.blobCache;
              var base64 = reader.result.split(',')[1];
              var blobInfo = blobCache.create(id, file, base64);
              blobCache.add(blobInfo);
              cb(blobInfo.blobUri(), { title: file.name });
            };
            reader.readAsDataURL(file);
          }
        };
        input.click();
      },
      setup: function(editor) {
        // Placeholder for custom buttons if needed
      }
    });

    // Add event listener for form submission
    var addContentForm = document.getElementById('addContentForm');
    if (addContentForm) {
      addContentForm.addEventListener('submit', function(e) {
        // Save TinyMCE content to the original textarea
        if (typeof tinymce !== 'undefined' && tinymce.get('editor')) {
          tinymce.triggerSave();
        }
      });
    }
  });
</script>
