import re

file_path = r'c:\Users\Itran\Desktop\Fundacion\assets\css\loading.css'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the loader-text block
old_text_block = r"""/* Stunning Gradient Shimmer Text */
.loader-text {
  font-family: var(--font-main);
  font-size: 2.2rem;
  font-weight: 800;
  letter-spacing: 2px;
  margin: 0;
  
  background: linear-gradient(
    to right,
    var(--accent-purple) 20%,
    var(--accent-blue) 40%,
    var(--accent-teal) 60%,
    var(--accent-purple) 80%
  );
  background-size: 200% auto;
  color: transparent;
  -webkit-background-clip: text;
  background-clip: text;
  animation: shine 4s linear infinite;
  
  text-shadow: 0 10px 20px rgba(0,0,0,0.08);
}

@keyframes shine {
  to {
    background-position: 200% center;
  }
}"""

new_text_block = r"""/* Stunning Gradient Shimmer Text with Elegant Intro Transition */
.loader-text {
  font-family: var(--font-main), sans-serif;
  font-size: 2.2rem;
  font-weight: 800;
  letter-spacing: 4px;
  margin: 0;
  margin-top: 15px;
  
  /* Fallback colors just in case variables fail to load in time */
  background: linear-gradient(
    to right,
    #6B46C1 20%,
    #3B82F6 40%,
    #14B8A6 60%,
    #6B46C1 80%
  );
  background-size: 200% auto;
  color: transparent;
  -webkit-background-clip: text;
  background-clip: text;
  
  /* Extremely elegant text entrance: blurred, tracking-in, fading in */
  animation: elegantTextEntrance 2s cubic-bezier(0.2, 1, 0.3, 1) forwards, shine 4s linear infinite;
  opacity: 0;
  filter: blur(12px);
  transform: translateY(15px) scale(0.95);
}

@keyframes elegantTextEntrance {
  to {
    opacity: 1;
    filter: blur(0px);
    transform: translateY(0) scale(1);
    letter-spacing: 2px; /* Smoothly reduces letter spacing */
  }
}

@keyframes shine {
  to {
    background-position: 200% center;
  }
}"""

if "elegantTextEntrance" not in content:
    content = content.replace(old_text_block, new_text_block)
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Updated text animation in loading.css!")
else:
    print("Already updated!")
