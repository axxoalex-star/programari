<!-- Icon Picker Component -->
<div class="form-group">
    <label for="icon">Iconiță *</label>
    <div style="display: flex; gap: 10px; align-items: center;">
        <input type="text" id="icon" name="icon" value="{{ old('icon', $selectedIcon ?? '') }}" readonly required style="flex: 1; cursor: pointer; background: #f8f9fa;" onclick="openIconPicker()">
        <div id="iconPreview" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border: 2px solid #ddd; border-radius: 8px; font-size: 28px; background: white;">
            @if(old('icon', $selectedIcon ?? ''))
                <i class="fa {{ old('icon', $selectedIcon ?? '') }}"></i>
            @endif
        </div>
        <button type="button" class="btn btn-sm btn-primary" onclick="openIconPicker()">Alege Iconiță</button>
    </div>
    @error('icon')<span style="color:#dc3545; font-size:13px;">{{ $message }}</span>@enderror
</div>

<!-- Icon Picker Modal -->
<div id="iconPickerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
    <div style="background: white; max-width: 900px; margin: 50px auto; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <div style="padding: 20px; border-bottom: 2px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; color: #333;">Alege Iconiță Font Awesome</h2>
            <button type="button" onclick="closeIconPicker()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">&times;</button>
        </div>
        <div style="padding: 20px;">
            <input type="text" id="iconSearch" placeholder="Caută iconiță (ex: heart, user, home)..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; margin-bottom: 20px;">
            <div id="iconGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; max-height: 500px; overflow-y: auto; padding: 10px;">
                <!-- Icons will be populated by JavaScript -->
            </div>
            <div id="noResults" style="display: none; text-align: center; padding: 40px; color: #999;">
                <p>Nu s-au găsit iconiță corespunzătoare căutării.</p>
            </div>
        </div>
    </div>
</div>

<style>
.icon-item {
    padding: 15px;
    text-align: center;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}
.icon-item:hover {
    border-color: #667eea;
    background: #f8f9ff;
    transform: translateY(-2px);
}
.icon-item i {
    font-size: 32px;
    color: #333;
    display: block;
    margin-bottom: 5px;
}
.icon-item span {
    font-size: 11px;
    color: #666;
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<script>
// Font Awesome 4 Icons - Medical & Veterinary selection
const faIcons = [
    // Medical - General
    'fa-stethoscope', 'fa-user-md', 'fa-medkit', 'fa-hospital-o', 'fa-ambulance',
    'fa-h-square', 'fa-plus-square', 'fa-heartbeat', 'fa-heart', 'fa-heart-o',

    // Medical - Body & Organs
    'fa-child', 'fa-male', 'fa-female', 'fa-wheelchair', 'fa-bed',
    'fa-eye', 'fa-eye-slash', 'fa-deaf', 'fa-assistive-listening-systems',

    // Medical - Treatment & Medicine
    'fa-thermometer', 'fa-thermometer-half', 'fa-thermometer-full', 'fa-thermometer-empty',
    'fa-syringe', 'fa-prescription', 'fa-briefcase-medical', 'fa-first-aid',
    'fa-plus', 'fa-band-aid', 'fa-pills', 'fa-tablets', 'fa-capsules',

    // Medical - Dental
    'fa-tooth',

    // Medical - Lab & Science
    'fa-microscope', 'fa-flask', 'fa-vial', 'fa-vials', 'fa-dna',
    'fa-virus', 'fa-bacteria', 'fa-disease', 'fa-allergies',

    // Animals - Pets
    'fa-paw', 'fa-bone', 'fa-dog', 'fa-cat',

    // Animals - Other
    'fa-bug', 'fa-spider', 'fa-crow', 'fa-dove', 'fa-dragon',
    'fa-fish', 'fa-frog', 'fa-hippo', 'fa-horse', 'fa-horse-head',
    'fa-otter', 'fa-kiwi-bird',

    // Nature & Environment
    'fa-leaf', 'fa-seedling', 'fa-tree', 'fa-carrot', 'fa-apple-alt',

    // Care & Support
    'fa-hand-holding-heart', 'fa-hands-helping', 'fa-hand-holding-medical',
    'fa-ribbon', 'fa-award', 'fa-shield-alt', 'fa-user-nurse',

    // Emergency & Safety
    'fa-fire-extinguisher', 'fa-exclamation-triangle', 'fa-exclamation-circle',
    'fa-info-circle', 'fa-check-circle', 'fa-times-circle'
];

let allIcons = [];

function openIconPicker() {
    document.getElementById('iconPickerModal').style.display = 'block';
    if (allIcons.length === 0) {
        renderIcons(faIcons);
        allIcons = faIcons;
    }
}

function closeIconPicker() {
    document.getElementById('iconPickerModal').style.display = 'none';
    document.getElementById('iconSearch').value = '';
}

function selectIcon(iconClass) {
    document.getElementById('icon').value = iconClass;
    document.getElementById('iconPreview').innerHTML = '<i class="fa ' + iconClass + '"></i>';
    closeIconPicker();
}

function renderIcons(icons) {
    const grid = document.getElementById('iconGrid');
    const noResults = document.getElementById('noResults');
    
    if (icons.length === 0) {
        grid.style.display = 'none';
        noResults.style.display = 'block';
        return;
    }
    
    grid.style.display = 'grid';
    noResults.style.display = 'none';
    
    grid.innerHTML = icons.map(icon => {
        const name = icon.replace('fa-', '').replace(/-/g, ' ');
        return `
            <div class="icon-item" onclick="selectIcon('${icon}')">
                <i class="fa ${icon}"></i>
                <span>${name}</span>
            </div>
        `;
    }).join('');
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('iconSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            if (searchTerm === '') {
                renderIcons(allIcons);
            } else {
                const filtered = allIcons.filter(icon => 
                    icon.toLowerCase().includes(searchTerm)
                );
                renderIcons(filtered);
            }
        });
    }
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeIconPicker();
        }
    });
});
</script>
