import os

dir_path = r'c:\Users\Itran\Desktop\Fundacion'

for filename in os.listdir(dir_path):
    if filename.endswith('.html'):
        filepath = os.path.join(dir_path, filename)
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # If the file ends with </html> but doesn't have </body> before it
        if '</html>' in content and '</body>' not in content:
            content = content.replace('</html>', '</body>\n</html>')
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Fixed {filename}")

