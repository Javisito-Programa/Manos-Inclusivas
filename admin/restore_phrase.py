import glob
import re

html_files = glob.glob(r'c:\Users\Itran\Desktop\Fundacion\*.html')

for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # In the previous run, we replaced <h2 class="loader-text">...</h2> with just <h2 class="loader-text">
    # We need to restore it to: <h2 class="loader-text">Iluminando Corazones... Conectando Vidas.</h2>
    target_pattern = r'<h2 class="loader-text">(\s*</div>)?\s*</div>'
    replacement = r'<h2 class="loader-text">Iluminando Corazones... Conectando Vidas.</h2>\n        </div>\n    </div>'
    
    # Wait, let's just do a simple replace since it was broken exactly the same way everywhere
    # It probably looks like:
    # <h2 class="loader-text">
    #     </div>
    # </div>
    
    # Safer regex:
    new_content = re.sub(r'<h2 class="loader-text">[\s\r\n]*</div>', '<h2 class="loader-text">Iluminando Corazones... Conectando Vidas.</h2>\n        </div>', content)
    
    if new_content != content:
        with open(f, 'w', encoding='utf-8') as file:
            file.write(new_content)
        print(f"Restored phrase in {f}")
    else:
        print(f"No changes needed for {f}")

print("Done restoring phrases!")
