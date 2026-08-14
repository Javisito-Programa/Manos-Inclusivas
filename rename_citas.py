import glob

for f in glob.glob('*.html'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    content = content.replace('clinicas-citas.html', 'citas.html')
    content = content.replace('CLÍNICAS Y CITAS', 'CITAS')
    content = content.replace('Clínicas y Citas', 'Citas')
    
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
