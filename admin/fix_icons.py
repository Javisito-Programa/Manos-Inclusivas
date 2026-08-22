import re

with open(r'c:\Users\Itran\Desktop\Fundacion\index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix Hidroterapia
content = content.replace('class="card-icon" style="color: var(--accent-blue);"', 'class="card-icon icon-blue"')

# Fix Nutricion (was orange)
content = content.replace('class="card-icon" style="color: var(--accent-orange);"', 'class="card-icon icon-purple"')

# Fix Mindfullness (was teal)
content = content.replace('class="card-icon" style="color: var(--accent-teal);"', 'class="card-icon icon-green"')

with open(r'c:\Users\Itran\Desktop\Fundacion\index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("Icons fixed in index.html!")
