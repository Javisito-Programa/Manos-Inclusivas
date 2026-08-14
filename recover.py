import json
import os

transcript_path = r"C:\Users\Itran\.gemini\antigravity-ide\brain\fe44dc10-c527-4083-a3cd-792fa47b6cb6\.system_generated\logs\transcript_full.jsonl"
files = {"quienes-somos.html": "", "noticias.html": "", "admin-noticias.html": ""}

with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        try:
            event = json.loads(line)
        except Exception:
            continue
        
        # User explicit code actions?
        if event.get('type') == 'CODE_ACTION':
            # This is complex to replay, let's hope no user code action on these 3 files.
            pass
            
        if event.get('type') == 'PLANNER_RESPONSE' and 'tool_calls' in event:
            for tc in event['tool_calls']:
                name = tc.get('name')
                args = tc.get('args', {})
                if not args: continue
                target_file = args.get('TargetFile', '')
                if not target_file: continue
                
                filename = os.path.basename(target_file.strip('"').replace('\\\\', '\\'))
                if filename in files:
                    if name == 'write_to_file':
                        val = args.get('CodeContent', '')
                        if val.startswith('"') and val.endswith('"'):
                            try:
                                val = json.loads(val)
                            except:
                                val = val[1:-1].replace('\\n', '\n').replace('\\"', '"')
                        files[filename] = val
                    elif name == 'replace_file_content':
                        target = args.get('TargetContent', '')
                        rep = args.get('ReplacementContent', '')
                        if target.startswith('"') and target.endswith('"'):
                            try:
                                target = json.loads(target)
                            except:
                                target = target[1:-1].replace('\\n', '\n').replace('\\"', '"')
                        if rep.startswith('"') and rep.endswith('"'):
                            try:
                                rep = json.loads(rep)
                            except:
                                rep = rep[1:-1].replace('\\n', '\n').replace('\\"', '"')
                        
                        if target in files[filename]:
                            files[filename] = files[filename].replace(target, rep)
                        else:
                            # Try normalizing line endings
                            target_norm = target.replace('\r\n', '\n')
                            file_norm = files[filename].replace('\r\n', '\n')
                            if target_norm in file_norm:
                                files[filename] = file_norm.replace(target_norm, rep)

for fn, content in files.items():
    if content:
        with open(fn, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Recovered {fn}")
