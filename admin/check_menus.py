import glob
import re

html_files = glob.glob(r'c:\Users\Itran\Desktop\Fundacion\*.html')
missing = []

for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Check if the dropdown menu specifically has Hidroterapia
    if not re.search(r'<li>\s*<a href="servicios\.html#hidroterapia"', content):
        missing.append(f)

print("Files missing hidroterapia in dropdown:", missing)
