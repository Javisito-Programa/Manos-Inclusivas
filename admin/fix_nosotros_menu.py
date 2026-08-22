import re

with open('nosotros.html', 'r', encoding='utf-8') as f:
    content = f.read()

target = r'(<li><a href="servicios.html#nutricion" class="dropdown-item">Nutrición</a></li>)'
replacement = r'<li><a href="servicios.html#hidroterapia" class="dropdown-item">Hidroterapia</a></li>\n                        \1'

new_content = re.sub(target, replacement, content)

with open('nosotros.html', 'w', encoding='utf-8') as f:
    f.write(new_content)
print("Updated nosotros.html")
