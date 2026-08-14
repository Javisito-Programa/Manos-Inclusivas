import re

with open('quienes-somos.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Remove style block
content = re.sub(r'<style>.*?</style>', '', content, flags=re.DOTALL)

# Rename classes
content = content.replace('about-hero', 'page-hero')
content = content.replace('about-content-grid', 'page-content-grid')
content = content.replace('about-sidebar', 'page-sidebar')
content = content.replace('about-section-card', 'page-section-card')

with open('quienes-somos.html', 'w', encoding='utf-8') as f:
    f.write(content)
