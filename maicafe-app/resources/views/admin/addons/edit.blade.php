@extends('layouts.admin')

@section('title', 'Edit Addon Group - Mai Cafe Admin')

@section('content')
<div class="page-header">
    <h1>Edit Addon Group</h1>
    <a class="btn" href="{{ route('admin.addons.index') }}" style="background: #f3f4f6; color: #374151;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form action="{{ route('admin.addons.update', $addon) }}" method="POST" id="addonForm">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">Group Name*</label>
            <input type="text" name="name" id="name" value="{{ old('name', $addon->name) }}" required placeholder="e.g., Milk Options, Extra Toppings">
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" id="description" value="{{ old('description', $addon->description) }}" placeholder="e.g., Choose your preferred milk type">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="selection_type">Selection Type*</label>
                <select name="selection_type" id="selection_type" required>
                    <option value="multiple" {{ old('selection_type', $addon->selection_type) == 'multiple' ? 'selected' : '' }}>Multiple Select (Checkboxes)</option>
                    <option value="single" {{ old('selection_type', $addon->selection_type) == 'single' ? 'selected' : '' }}>Single Select (Radio)</option>
                </select>
                <small style="color: #6b7280; margin-top: 4px; display: block;">Multiple: customers can select several options. Single: only one option allowed.</small>
            </div>

            <div class="form-group">
                <div class="checkbox-group" style="margin-top: 28px;">
                    <input type="checkbox" name="is_required" id="is_required" value="1" {{ old('is_required', $addon->is_required) ? 'checked' : '' }}>
                    <label for="is_required">Required Selection</label>
                </div>
                <small style="color: #6b7280; margin-top: 4px; display: block;">If checked, customer must select at least one option.</small>
            </div>
        </div>

        <div class="form-grid" id="selectionLimits" style="display: none;">
            <div class="form-group">
                <label for="min_selections">Minimum Selections</label>
                <input type="number" name="min_selections" id="min_selections" value="{{ old('min_selections', $addon->min_selections) }}" min="0">
            </div>

            <div class="form-group">
                <label for="max_selections">Maximum Selections</label>
                <input type="number" name="max_selections" id="max_selections" value="{{ old('max_selections', $addon->max_selections) }}" min="1" placeholder="Leave empty for unlimited">
            </div>
        </div>

        <!-- Addon Options Section -->
        <div style="margin: 24px 0; padding: 20px; background: #f9fafb; border-radius: 8px;">
            <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                <label style="font-weight: 600; font-size: 15px;">Addon Options*</label>
                <button type="button" class="btn btn-primary" onclick="addAddonOption()" style="padding: 8px 16px;">
                    <i class="fas fa-plus"></i> Add Option
                </button>
            </div>
            
            <div id="addonOptionsList">
                <!-- Options will be loaded here -->
            </div>
            
            @error('addons')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $addon->is_active) ? 'checked' : '' }}>
                <label for="is_active">Active</label>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update Addon Group
            </button>
        </div>
    </form>
</div>

<style>
.addon-option-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.addon-option-card .option-fields {
    flex: 1;
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 12px;
}
.addon-option-card .form-group {
    margin-bottom: 0;
}
.addon-option-card label {
    font-size: 12px;
    color: #6b7280;
}
.addon-option-card input {
    padding: 8px 10px;
    font-size: 13px;
}
.addon-option-card .remove-option {
    background: #fee2e2;
    color: #dc2626;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 20px;
}
.addon-option-card .remove-option:hover {
    background: #fecaca;
}
</style>

<script>
let optionIndex = 0;

// Existing addons from server
const existingAddons = @json($addon->addons ?? []);

function toggleSelectionLimits() {
    const selectionType = document.getElementById('selection_type').value;
    const selectionLimits = document.getElementById('selectionLimits');
    selectionLimits.style.display = selectionType === 'multiple' ? 'grid' : 'none';
}

function addAddonOption(defaultData = null) {
    const data = defaultData || { id: '', name: '', price: '' };
    const optionsList = document.getElementById('addonOptionsList');
    const optionHtml = `
        <div class="addon-option-card" id="option-${optionIndex}">
            <input type="hidden" name="addons[${optionIndex}][id]" value="${data.id || ''}">
            <div class="option-fields">
                <div class="form-group">
                    <label>Option Name*</label>
                    <input type="text" name="addons[${optionIndex}][name]" value="${data.name || ''}" required placeholder="e.g., Oat Milk, Extra Shot">
                </div>
                <div class="form-group">
                    <label>Additional Price (£)*</label>
                    <input type="number" step="0.01" name="addons[${optionIndex}][price]" value="${data.price || ''}" required min="0" placeholder="0.00">
                </div>
            </div>
            <button type="button" class="remove-option" onclick="removeAddonOption(${optionIndex})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    optionsList.insertAdjacentHTML('beforeend', optionHtml);
    optionIndex++;
}

function removeAddonOption(index) {
    const option = document.getElementById(`option-${index}`);
    if (option) {
        option.remove();
    }
    
    // Ensure at least one option exists
    if (document.querySelectorAll('.addon-option-card').length === 0) {
        addAddonOption();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleSelectionLimits();
    document.getElementById('selection_type').addEventListener('change', toggleSelectionLimits);
    
    // Load existing addons
    if (existingAddons.length > 0) {
        existingAddons.forEach(addon => {
            addAddonOption(addon);
        });
    } else {
        addAddonOption();
    }
});
</script>
@endsection
