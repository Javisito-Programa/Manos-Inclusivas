import glob
import re

html_files = glob.glob(r'c:\Users\Itran\Desktop\Fundacion\*.html')

for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Check if the exact dropdown item exists
    if not re.search(r'<li>\s*<a href="servicios\.html#hidroterapia"\s*class="dropdown-item">Hidroterapia</a>\s*</li>', content):
        print(f"MISSING in {f}")
        
        # FIX IT
        target = r'(<li>\s*<a href="servicios\.html#nutricion"\s*class="dropdown-item">Nutrición</a>\s*</li>)'
        replacement = r'<li><a href="servicios.html#hidroterapia" class="dropdown-item">Hidroterapia</a></li>\n                        \1'
        
        new_content = re.sub(target, replacement, content)
        
        if new_content == content:
            target2 = r'(<li[^>]*>\s*<a href="servicios\.html#nutricion"[^>]*>Nutrici[^<]*</a>\s*</li>)'
            new_content = re.sub(target2, replacement, content)
            
        if new_content != content:
            with open(f, 'w', encoding='utf-8') as file:
                file.write(new_content)
            print(f"FIXED {f}")
        else:
            print(f"COULD NOT FIX {f} - couldn't find Nutricion anchor")
    else:
        print(f"OK {f}")
