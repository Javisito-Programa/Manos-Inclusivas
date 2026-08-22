import glob
import re

html_files = glob.glob(r'c:\Users\Itran\Desktop\Fundacion\*.html')

for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Target to replace
    target_pattern = r'(<div class="loader-content">)\s*(<model-viewer[^>]*class="loader-3d-logo"></model-viewer>)\s*(<h2 class="loader-text">)'
    replacement = r'\1\n            <div class="loader-logo-container">\n                \2\n            </div>\n            \3'
    
    new_content = re.sub(target_pattern, replacement, content)
    
    if new_content != content:
        with open(f, 'w', encoding='utf-8') as file:
            file.write(new_content)
        print(f"Updated {f}")
    else:
        print(f"Skipped {f}")

print("Done wrapping loader-3d-logo!")
