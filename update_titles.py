import glob, re
files = glob.glob('*.html')
for f in files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    if 'class="notranslate" translate="no"' not in content:
        content = re.sub(r'<title.*?>', '<title class="notranslate" translate="no">', content)
        with open(f, 'w', encoding='utf-8') as file:
            file.write(content)
print('Titles updated for', len(files), 'files')
