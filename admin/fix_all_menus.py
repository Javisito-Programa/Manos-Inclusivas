import glob
import re

html_files = glob.glob(r'c:\Users\Itran\Desktop\Fundacion\*.html')

for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Check if the dropdown menu specifically has Hidroterapia
    if not re.search(r'<li><a href="servicios\.html#hidroterapia" class="dropdown-item">Hidroterapia</a></li>', content):
        # We need to insert it right before Nutricion in the dropdown
        target = r'(<li><a href="servicios\.html#nutricion" class="dropdown-item">Nutrición</a></li>)'
        replacement = r'<li><a href="servicios.html#hidroterapia" class="dropdown-item">Hidroterapia</a></li>\n                        \1'
        
        # In some files, "Nutrición" might have a different character encoding, so let's be flexible
        target2 = r'(<li[^>]*><a href="servicios\.html#nutricion"[^>]*>Nutrici.n</a></li>)'
        
        # Let's just use re.sub with a flexible regex
        new_content = re.sub(target, replacement, content)
        
        if new_content == content:
             new_content = re.sub(target2, replacement, content)
        
        if new_content != content:
            with open(f, 'w', encoding='utf-8') as file:
                file.write(new_content)
            print(f"Fixed menu in {f}")
        else:
            print(f"Could not find target in {f}")

print("Done")
