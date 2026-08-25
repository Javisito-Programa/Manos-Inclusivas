import os
import glob
from PIL import Image

def convert_to_webp(source):
    destination = source.rsplit('.', 1)[0] + '.webp'
    try:
        image = Image.open(source)
        # Convert RGBA to RGB if saving as webp without alpha, but WebP supports alpha.
        image.save(destination, format="webp", quality=85)
        print(f"Converted: {source} -> {destination}")
        os.remove(source)
        return True
    except Exception as e:
        print(f"Error converting {source}: {e}")
        return False

# 1. Find all images
directories = ['img', 'assets', 'admin/uploads']
extensions = ['*.jpg', '*.jpeg', '*.png']
converted_files = []

for directory in directories:
    for ext in extensions:
        # Recursive glob
        for filepath in glob.glob(f'{directory}/**/{ext}', recursive=True):
            if convert_to_webp(filepath):
                # Store relative filename without extension for replacement
                basename = os.path.basename(filepath).rsplit('.', 1)[0]
                old_ext = filepath.rsplit('.', 1)[1]
                converted_files.append((basename, old_ext))

# 2. Update references in code
code_extensions = ['*.html', '*.php', '*.css', '*.json']
code_files = []
for ext in code_extensions:
    code_files.extend(glob.glob(f'**/{ext}', recursive=True))

for filepath in code_files:
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        for basename, old_ext in converted_files:
            # Replace exact matches like basename.jpg -> basename.webp
            content = content.replace(f"{basename}.{old_ext}", f"{basename}.webp")
            # Handle URL encoding if any (like "Logo circular.png" -> "Logo%20circular.png")
            encoded_basename = basename.replace(' ', '%20')
            if encoded_basename != basename:
                content = content.replace(f"{encoded_basename}.{old_ext}", f"{encoded_basename}.webp")

        if content != original_content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Updated references in: {filepath}")
    except Exception as e:
        print(f"Error updating {filepath}: {e}")
