import os
import re

dir_path = r'c:\Users\Itran\Desktop\Fundacion'

# 1. Read index.html to get the footer
index_path = os.path.join(dir_path, 'index.html')
with open(index_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Use regex to find <footer>...</footer>
footer_match = re.search(r'<footer>.*?</footer>', content, flags=re.DOTALL)
if not footer_match:
    print("Could not find footer in index.html")
    exit(1)

new_footer = footer_match.group(0)

# 2. Iterate through all html files
files_modified = 0
for filename in os.listdir(dir_path):
    if filename.endswith('.html') and filename != 'index.html':
        filepath = os.path.join(dir_path, filename)
        with open(filepath, 'r', encoding='utf-8') as f:
            file_content = f.read()
        
        # Check if the file has a footer
        if re.search(r'<footer>.*?</footer>', file_content, flags=re.DOTALL):
            # Replace it
            new_file_content = re.sub(r'<footer>.*?</footer>', new_footer, file_content, flags=re.DOTALL)
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_file_content)
            files_modified += 1
            print(f"Updated footer in {filename}")
        else:
            print(f"No footer found in {filename}")

print(f"Total files updated: {files_modified}")
