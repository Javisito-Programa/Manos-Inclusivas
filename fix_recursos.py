import re

with open('recursos.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Remove inline styles
content = re.sub(r'<style>.*?</style>', '', content, flags=re.DOTALL)

# Replace the text-center header with the hero
old_hero = r'''<main class="section-padding bg-beige">
        <div class="container text-center" style="margin-bottom: 50px;">
            <h1 style="color: var(--text-main); font-size: 2.5rem;">Biblioteca de Recursos</h1>
            <p>Información clara y profesional sobre condiciones del neurodesarrollo.</p>
        </div>'''
new_hero = r'''<main class="bg-beige">
        <section class="page-hero">
            <svg class="bg-shape shape-1 decorative-circle circle-1" viewBox="0 0 100 100"></svg>
            <svg class="bg-shape shape-2 decorative-circle circle-2" viewBox="0 0 100 100"></svg>
            <h1>Biblioteca de Recursos</h1>
            <p>Información clara y profesional sobre condiciones del neurodesarrollo.</p>
        </section>'''

if old_hero in content:
    content = content.replace(old_hero, new_hero)
else:
    # Try more flexible replacement just in case
    content = re.sub(r'<main class="section-padding bg-beige">.*?</div>', new_hero, content, flags=re.DOTALL, count=1)

# Update grid
content = content.replace('container resources-grid', 'container page-content-grid')

# Update sidebar
content = content.replace('class="sidebar"', 'class="page-sidebar"')
content = content.replace('class="sidebar-link"', 'class="sidebar-nav-link"')

# Update cards
content = content.replace('class="resource-article"', 'class="page-section-card"')

with open('recursos.html', 'w', encoding='utf-8') as f:
    f.write(content)
