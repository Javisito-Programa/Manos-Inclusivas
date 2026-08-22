import glob
import re

# 1. Update dropdown menus in all HTML files
html_files = glob.glob(r'c:\Users\Itran\Desktop\Fundacion\*.html')
for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    target_dropdown = r'<li><a href="servicios.html#integracion-sensorial" class="dropdown-item">Integración Sensorial</a></li>'
    new_dropdown = '''<li><a href="servicios.html#integracion-sensorial" class="dropdown-item">Integración Sensorial</a></li>
                        <li><a href="servicios.html#hidroterapia" class="dropdown-item">Hidroterapia</a></li>'''
    
    content = content.replace(target_dropdown, new_dropdown)
    
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)

# 2. Add missing buttons in servicios.html and the new section
with open(r'c:\Users\Itran\Desktop\Fundacion\servicios.html', 'r', encoding='utf-8') as file:
    s_content = file.read()

# Fix Terapia Ocupacional missing button
target_oc = r'''                        <h2>Terapia Ocupacional Infantil</h2>
                        <p>Intervención centrada en dotar de herramientas al paciente para realizar sus actividades de
                            la vida diaria (higiene, alimentación, juego, escuela) con la mayor independencia posible,
                            desarrollando motricidad fina y gruesa.</p>
                    </div>'''
new_oc = r'''                        <h2>Terapia Ocupacional Infantil</h2>
                        <p>Intervención centrada en dotar de herramientas al paciente para realizar sus actividades de
                            la vida diaria (higiene, alimentación, juego, escuela) con la mayor independencia posible,
                            desarrollando motricidad fina y gruesa.</p>
                        <a href="citas.html" class="btn btn-outline" style="margin-top: 15px;">Agendar Cita</a>
                    </div>'''
s_content = s_content.replace(target_oc, new_oc)

# Fix Integracion Sensorial missing button and add Hidroterapia right after it
target_is = r'''                        <h2>Integración Sensorial</h2>
                        <p>Terapia especializada en salas sensoriales adaptadas para ayudar a los pacientes a procesar,
                            organizar y responder adecuadamente a la información que reciben a través de sus sentidos
                            (tacto, movimiento, audición, visión).</p>
                    </div>
                </div>'''
new_is = r'''                        <h2>Integración Sensorial</h2>
                        <p>Terapia especializada en salas sensoriales adaptadas para ayudar a los pacientes a procesar,
                            organizar y responder adecuadamente a la información que reciben a través de sus sentidos
                            (tacto, movimiento, audición, visión).</p>
                        <a href="citas.html" class="btn btn-outline" style="margin-top: 15px;">Agendar Cita</a>
                    </div>
                </div>

                <div id="hidroterapia" class="service-detail-section">
                    <div class="service-image">
                        <img src="img/Hidroterapia.jpg" alt="Imagen del servicio"
                            style="width: 100%; height: auto; border-radius: var(--border-radius-soft); box-shadow: var(--shadow-soft);">
                    </div>
                    <div class="service-content">
                        <div class="service-icon-large" style="color: var(--accent-blue);">💧</div>
                        <h2>Hidroterapia para niños con Trastorno del Espectro Autista</h2>
                        <p>Terapia acuática especializada que utiliza las propiedades físicas del agua para facilitar el movimiento, promover la relajación muscular y mejorar la integración sensorial. Ideal para reducir la ansiedad y mejorar la motricidad en un ambiente lúdico y seguro.</p>
                        <a href="citas.html" class="btn btn-outline" style="margin-top: 15px;">Agendar Cita</a>
                    </div>
                </div>'''
s_content = s_content.replace(target_is, new_is)

with open(r'c:\Users\Itran\Desktop\Fundacion\servicios.html', 'w', encoding='utf-8') as file:
    file.write(s_content)

# 3. Add card to index.html
with open(r'c:\Users\Itran\Desktop\Fundacion\index.html', 'r', encoding='utf-8') as file:
    i_content = file.read()

target_card = r'''                        <h3 class="card-title">Integración Sensorial</h3>
                        <p class="card-text">Ayudamos a procesar adecuadamente la información de los sentidos.</p>
                        <a href="servicios.html#integracion-sensorial" class="btn btn-outline"
                            style="padding: 8px 20px; font-size: 0.9rem;">Saber más</a>
                    </div>'''

new_card = r'''                        <h3 class="card-title">Integración Sensorial</h3>
                        <p class="card-text">Ayudamos a procesar adecuadamente la información de los sentidos.</p>
                        <a href="servicios.html#integracion-sensorial" class="btn btn-outline"
                            style="padding: 8px 20px; font-size: 0.9rem;">Saber más</a>
                    </div>
                    <!-- Card Hidroterapia -->
                    <div class="card">
                        <div class="card-icon" style="color: var(--accent-blue);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h4l3-9 5 18 3-9h5"/></svg>
                        </div>
                        <h3 class="card-title">Hidroterapia</h3>
                        <p class="card-text">Terapia acuática especializada para niños con autismo.</p>
                        <a href="servicios.html#hidroterapia" class="btn btn-outline"
                            style="padding: 8px 20px; font-size: 0.9rem;">Saber más</a>
                    </div>'''
i_content = i_content.replace(target_card, new_card)

with open(r'c:\Users\Itran\Desktop\Fundacion\index.html', 'w', encoding='utf-8') as file:
    file.write(i_content)

print("Actualización completada")
