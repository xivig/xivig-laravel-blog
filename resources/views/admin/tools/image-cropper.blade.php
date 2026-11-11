@extends('admin.layouts.admin')

@section('title', 'Advanced Image Cropper')
@section('parentTitle', 'Design & Media')

@section('content')
<link rel="stylesheet" href="{{ asset('vendor/cropperjs/cropper.min.css') }}">

<div class="row g-4">
    <!-- Header Section -->
    <div class="col-12">
        <div class="card border-0 rounded-4 bg-surface border border-subtle shadow-sm p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h3 class="fw-bold text-body mb-1">
                        <i class="bi bi-crop text-gold me-2"></i>Advanced Image Cropper
                    </h3>
                    <p class="text-secondary small mb-0">High-precision editorial asset framing, aspect constraints, and spatial transformations.</p>
                </div>
                <div class="d-flex gap-2">
                    <label class="btn btn-outline-gold rounded-pill px-4 py-2 cursor-pointer mb-0" for="inputImage">
                        <input type="file" class="visually-hidden" id="inputImage" accept="image/*">
                        <i class="bi bi-cloud-arrow-up me-2"></i>Import New Photo
                    </label>
                    <button type="button" class="btn btn-gold rounded-pill px-4 py-2 fw-semibold" id="btnExportTop">
                        <i class="bi bi-download me-2"></i>Export Asset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Cropper Workspace -->
    <div class="col-12">
        <div class="card border-0 rounded-4 bg-surface border border-subtle shadow-sm overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="row g-4 g-xl-5">
                    <!-- Left: Cropping Stage -->
                    <div class="col-xl-9 col-lg-8">
                        <div class="img-container rounded-4 overflow-hidden border border-subtle position-relative shadow-inner" 
                             style="min-height: 520px; max-height: 600px; background-color: #0b0d11; background-image: radial-gradient(rgba(201, 169, 110, 0.15) 1px, transparent 1px); background-size: 24px 24px; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset('assets/admin/images/img1.jpg') }}" id="image" alt="Editorial Asset" class="img-fluid" style="max-width: 100%;">
                        </div>

                        <!-- Interactive Toolbar -->
                        <div class="row mt-4 align-items-center" id="actions">
                            <div class="col-12 docs-buttons d-flex flex-wrap gap-2 justify-content-center justify-content-lg-start">
                                <!-- Mode Controls -->
                                <div class="btn-group shadow-xs">
                                    <button type="button" class="btn btn-outline-secondary" data-method="setDragMode" data-option="move" title="Move Mode">
                                        <i class="bi bi-arrows-move"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary active" data-method="setDragMode" data-option="crop" title="Crop Selection Mode">
                                        <i class="bi bi-crop"></i>
                                    </button>
                                </div>

                                <!-- Zoom Controls -->
                                <div class="btn-group shadow-xs">
                                    <button type="button" class="btn btn-outline-secondary" data-method="zoom" data-option="0.1" title="Zoom In (+10%)">
                                        <i class="bi bi-zoom-in"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-method="zoom" data-option="-0.1" title="Zoom Out (-10%)">
                                        <i class="bi bi-zoom-out"></i>
                                    </button>
                                </div>

                                <!-- Pan Controls -->
                                <div class="btn-group shadow-xs">
                                    <button type="button" class="btn btn-outline-secondary" data-method="move" data-option="-10" data-second-option="0" title="Pan Left">
                                        <i class="bi bi-arrow-left"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-method="move" data-option="10" data-second-option="0" title="Pan Right">
                                        <i class="bi bi-arrow-right"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-method="move" data-option="0" data-second-option="-10" title="Pan Up">
                                        <i class="bi bi-arrow-up"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-method="move" data-option="0" data-second-option="10" title="Pan Down">
                                        <i class="bi bi-arrow-down"></i>
                                    </button>
                                </div>

                                <!-- Rotation Controls -->
                                <div class="btn-group shadow-xs">
                                    <button type="button" class="btn btn-outline-secondary" data-method="rotate" data-option="-45" title="Rotate Counter-Clockwise (-45°)">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-method="rotate" data-option="45" title="Rotate Clockwise (+45°)">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>

                                <!-- Flip / Scale Controls -->
                                <div class="btn-group shadow-xs">
                                    <button type="button" class="btn btn-outline-secondary" data-method="scaleX" data-option="-1" title="Flip Horizontal">
                                        <i class="bi bi-arrows-expand"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-method="scaleY" data-option="-1" title="Flip Vertical">
                                        <i class="bi bi-arrows-expand-vertical"></i>
                                    </button>
                                </div>

                                <!-- State Controls -->
                                <div class="btn-group shadow-xs">
                                    <button type="button" class="btn btn-outline-danger" data-method="clear" title="Clear Crop Box">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-method="disable" title="Lock Studio">
                                        <i class="bi bi-lock"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success" data-method="enable" title="Unlock Studio">
                                        <i class="bi bi-unlock"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" data-method="reset" title="Reset Changes">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </div>

                                <!-- Quick Export Trigger -->
                                <button type="button" class="btn btn-gold px-3 shadow-xs d-flex align-items-center" 
                                        data-method="getCroppedCanvas">
                                    <i class="bi bi-scissors me-2"></i> Crop & Preview
                                </button>
                            </div>

                            <!-- Aspect Ratio Selectors -->
                            <div class="col-12 mt-4 pt-3 border-top border-subtle">
                                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-secondary extra-small text-uppercase fw-bold letter-spacing-1">Aspect Ratio:</span>
                                        <div class="btn-group docs-toggles" role="group">
                                            <button type="button" class="btn btn-sm btn-gold px-3 active" data-method="setAspectRatio" data-option="1.7777777777777777">16:9</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-method="setAspectRatio" data-option="1.3333333333333333">4:3</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-method="setAspectRatio" data-option="1">1:1</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-method="setAspectRatio" data-option="0.6666666666666666">2:3</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-method="setAspectRatio" data-option="NaN">Free</button>
                                        </div>
                                    </div>

                                    <div class="dropdown docs-toggles">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                                            More Presets
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-subtle p-2">
                                            <li><a class="dropdown-item rounded-2" href="#" data-method="setAspectRatio" data-option="2.35">Cinemascope (21:9)</a></li>
                                            <li><a class="dropdown-item rounded-2" href="#" data-method="setAspectRatio" data-option="0.8">Instagram Portrait (4:5)</a></li>
                                            <li><a class="dropdown-item rounded-2" href="#" data-method="setAspectRatio" data-option="0.5625">Story / Reel (9:16)</a></li>
                                            <li><hr class="dropdown-divider border-subtle"></li>
                                            <li><a class="dropdown-item rounded-2" href="#" data-method="setAspectRatio" data-option="NaN">Free Unconstrained</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Live Preview & Spatial Telemetry -->
                    <div class="col-xl-3 col-lg-4">
                        <div class="p-4 rounded-4 bg-body border border-subtle h-100 shadow-sm d-flex flex-column justify-content-between">
                            <div>
                                <label class="text-gold fw-bold text-uppercase extra-small letter-spacing-1 d-block mb-3">
                                    <i class="bi bi-eye-fill me-1"></i> Live Preview
                                </label>
                                
                                <div class="docs-preview d-flex flex-column gap-3 mb-4">
                                    <div class="img-preview preview-lg rounded-3 border border-subtle bg-black shadow-xs overflow-hidden" 
                                         style="height: 160px; width: 100%;"></div>
                                    <div class="d-flex gap-2">
                                        <div class="img-preview preview-md rounded-2 border border-subtle bg-black shadow-xs overflow-hidden" 
                                             style="height: 70px; width: 120px;"></div>
                                        <div class="img-preview preview-sm rounded-circle border border-gold bg-black shadow-xs overflow-hidden" 
                                             style="height: 70px; width: 70px;"></div>
                                    </div>
                                </div>

                                <label class="text-gold fw-bold text-uppercase extra-small letter-spacing-1 d-block mb-3">
                                    <i class="bi bi-bounding-box me-1"></i> Spatial Telemetry
                                </label>
                                
                                <div class="docs-data space-y-2">
                                    <div class="input-group input-group-sm mb-2 shadow-xs rounded-3 overflow-hidden border border-subtle">
                                        <span class="input-group-text bg-surface border-0 fw-bold px-3 text-gold" style="width: 48px;">X</span>
                                        <input type="text" class="form-control border-0 text-center bg-body text-body font-monospace" id="dataX" placeholder="0">
                                        <span class="input-group-text bg-surface border-0 text-secondary small">px</span>
                                    </div>
                                    <div class="input-group input-group-sm mb-2 shadow-xs rounded-3 overflow-hidden border border-subtle">
                                        <span class="input-group-text bg-surface border-0 fw-bold px-3 text-gold" style="width: 48px;">Y</span>
                                        <input type="text" class="form-control border-0 text-center bg-body text-body font-monospace" id="dataY" placeholder="0">
                                        <span class="input-group-text bg-surface border-0 text-secondary small">px</span>
                                    </div>
                                    <div class="input-group input-group-sm mb-2 shadow-xs rounded-3 overflow-hidden border border-subtle">
                                        <span class="input-group-text bg-surface border-0 fw-bold px-3 text-gold" style="width: 48px;">W</span>
                                        <input type="text" class="form-control border-0 text-center bg-body text-body font-monospace" id="dataWidth" placeholder="0">
                                        <span class="input-group-text bg-surface border-0 text-secondary small">px</span>
                                    </div>
                                    <div class="input-group input-group-sm mb-2 shadow-xs rounded-3 overflow-hidden border border-subtle">
                                        <span class="input-group-text bg-surface border-0 fw-bold px-3 text-gold" style="width: 48px;">H</span>
                                        <input type="text" class="form-control border-0 text-center bg-body text-body font-monospace" id="dataHeight" placeholder="0">
                                        <span class="input-group-text bg-surface border-0 text-secondary small">px</span>
                                    </div>
                                    <div class="input-group input-group-sm mb-2 shadow-xs rounded-3 overflow-hidden border border-subtle">
                                        <span class="input-group-text bg-surface border-0 fw-bold px-3 text-gold" style="width: 48px;">R</span>
                                        <input type="text" class="form-control border-0 text-center bg-body text-body font-monospace" id="dataRotate" placeholder="0">
                                        <span class="input-group-text bg-surface border-0 text-secondary small">deg</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top border-subtle">
                                <button class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-semibold small shadow-xs" data-method="reset">
                                    <i class="bi bi-arrow-repeat me-2"></i> Reset Coordinates
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cropped Result Modal -->
<div class="modal fade" id="getCroppedCanvasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg bg-surface border border-subtle overflow-hidden">
            <div class="modal-header border-bottom border-subtle p-4">
                <h5 class="modal-title fw-bold text-body">
                    <i class="bi bi-scissors text-gold me-2"></i> Cropped Editorial Asset
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center bg-body" id="croppedResult" style="min-height: 250px; display: flex; align-items: center; justify-content: center;">
                <!-- Result Canvas Injected Here -->
            </div>
            <div class="modal-footer border-top border-subtle p-4 justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <a class="btn btn-gold rounded-pill px-5 fw-bold" id="download" href="javascript:void(0);" download="maison-cropped-asset.jpg">
                    <i class="bi bi-download me-2"></i>Download Rendered Asset
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-1 {
        letter-spacing: 0.08em;
    }
    .shadow-inner {
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.4);
    }
    #croppedResult canvas {
        max-width: 100%;
        max-height: 480px;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    }
    .img-preview {
        background-color: #000;
        overflow: hidden;
    }
</style>

<script src="{{ asset('vendor/cropperjs/cropper.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const image = document.getElementById('image');
    const dataX = document.getElementById('dataX');
    const dataY = document.getElementById('dataY');
    const dataHeight = document.getElementById('dataHeight');
    const dataWidth = document.getElementById('dataWidth');
    const dataRotate = document.getElementById('dataRotate');
    
    let scaleX = 1;
    let scaleY = 1;

    const options = {
        aspectRatio: 16 / 9,
        preview: '.img-preview',
        viewMode: 1,
        responsive: true,
        restore: true,
        checkCrossOrigin: false,
        checkOrientation: false,
        modal: true,
        guides: true,
        center: true,
        highlight: true,
        background: true,
        autoCrop: true,
        autoCropArea: 0.8,
        movable: true,
        rotatable: true,
        scalable: true,
        zoomable: true,
        crop: function (event) {
            dataX.value = Math.round(event.detail.x);
            dataY.value = Math.round(event.detail.y);
            dataHeight.value = Math.round(event.detail.height);
            dataWidth.value = Math.round(event.detail.width);
            dataRotate.value = event.detail.rotate !== undefined ? event.detail.rotate : 0;
        }
    };

    let cropper = new Cropper(image, options);

    // Modal & export elements
    const modalElement = document.getElementById('getCroppedCanvasModal');
    const exportModal = new bootstrap.Modal(modalElement);
    const croppedResult = document.getElementById('croppedResult');
    const downloadBtn = document.getElementById('download');

    function triggerCropExport() {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 4096,
            maxHeight: 4096,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (canvas) {
            croppedResult.innerHTML = '';
            croppedResult.appendChild(canvas);
            downloadBtn.href = canvas.toDataURL('image/jpeg', 0.92);
            exportModal.show();
        }
    }

    document.getElementById('btnExportTop').addEventListener('click', triggerCropExport);

    // Actions button dispatcher
    document.querySelectorAll('[data-method]').forEach(function (button) {
        button.addEventListener('click', function (e) {
            const method = button.getAttribute('data-method');
            const option = button.getAttribute('data-option');
            const secondOption = button.getAttribute('data-second-option');

            if (!cropper) return;

            switch (method) {
                case 'setDragMode':
                    cropper.setDragMode(option);
                    document.querySelectorAll('[data-method="setDragMode"]').forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    break;
                case 'zoom':
                    cropper.zoom(parseFloat(option));
                    break;
                case 'move':
                    cropper.move(parseFloat(option), parseFloat(secondOption || 0));
                    break;
                case 'rotate':
                    cropper.rotate(parseFloat(option));
                    break;
                case 'scaleX':
                    scaleX = -scaleX;
                    cropper.scaleX(scaleX);
                    break;
                case 'scaleY':
                    scaleY = -scaleY;
                    cropper.scaleY(scaleY);
                    break;
                case 'clear':
                    cropper.clear();
                    break;
                case 'reset':
                    scaleX = 1;
                    scaleY = 1;
                    cropper.reset();
                    break;
                case 'disable':
                    cropper.disable();
                    break;
                case 'enable':
                    cropper.enable();
                    break;
                case 'setAspectRatio':
                    const ratio = option === 'NaN' ? NaN : parseFloat(option);
                    cropper.setAspectRatio(ratio);
                    document.querySelectorAll('[data-method="setAspectRatio"]').forEach(btn => {
                        btn.classList.remove('active', 'btn-gold');
                        btn.classList.add('btn-outline-secondary');
                    });
                    button.classList.remove('btn-outline-secondary');
                    button.classList.add('active', 'btn-gold');
                    break;
                case 'getCroppedCanvas':
                    triggerCropExport();
                    break;
            }
        });
    });

    // File Input Import
    const inputImage = document.getElementById('inputImage');
    if (inputImage) {
        inputImage.addEventListener('change', function () {
            const files = this.files;
            if (files && files.length) {
                const file = files[0];
                if (/^image\/\w+/.test(file.type)) {
                    const uploadedImageURL = URL.createObjectURL(file);
                    image.src = uploadedImageURL;
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(image, options);
                } else {
                    alert('Please select a valid image file (JPEG, PNG, WEBP).');
                }
            }
        });
    }

    // Manual Spatial Input updates
    [dataX, dataY, dataWidth, dataHeight].forEach(input => {
        input.addEventListener('change', function () {
            if (!cropper) return;
            cropper.setData({
                x: parseFloat(dataX.value) || 0,
                y: parseFloat(dataY.value) || 0,
                width: parseFloat(dataWidth.value) || 0,
                height: parseFloat(dataHeight.value) || 0,
                rotate: parseFloat(dataRotate.value) || 0
            });
        });
    });
});
</script>
@endsection
