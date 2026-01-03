<!-- All Styling files -->
<link rel="stylesheet" href="<?= base_url("assets/css/styles.css") ?>">
<link rel="stylesheet" href="<?= base_url("assets/css/new.css") ?>">
<link rel="stylesheet" href="<?= base_url("assets/css/dashboard.css") ?>">

<!-- Splade Styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
<link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet">

<!-- Summernote Styles -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<!-- Font Awesome Styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- JQuery Data tables Styles -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<!-- Select2 Styles -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Dropzone Styles -->
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">

<!-- Custom Summernote Fixes -->
<style>
    /* Prevent entire page scroll */
    html, body {
        overflow: hidden !important;
        height: 100%;
    }
    
    /* Ensure main container scrolls properly */
    main.overflow-y-auto {
        overflow-y: auto !important;
        height: 100%;
    }
    
    /* Prevent Summernote from causing page scroll */
    .note-editor.note-frame {
        position: relative;
        overflow: hidden !important;
    }
    
    .note-toolbar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: #f5f5f5;
        overflow: visible !important;
    }
    
    .note-editable {
        overflow-y: auto !important;
        max-height: 300px;
    }
    
    /* Ensure dropdowns are properly contained */
    .note-dropdown-menu {
        position: absolute !important;
        z-index: 1050 !important;
        max-height: 200px;
        overflow-y: auto;
    }
    
    /* Prevent toolbar button groups from overflowing */
    .note-btn-group {
        display: inline-flex;
    }
</style>