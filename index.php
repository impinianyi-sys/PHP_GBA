<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GBA ADVOS | Excel·lència en Dret</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --granate: #800020;
            --negro: #121212;
            --gris-fosc: #333333;
            --gris-clar: #f4f4f4;
            --blanc: #ffffff;
            --transicion: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--blanc);
            color: var(--negro);
            line-height: 1.6;
            overflow-x: hidden;
        }


        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 8%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: var(--transicion);
            background: transparent;
        }

        nav.scrolled {
            background: var(--negro);
            padding: 1rem 8%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .logo { color: var(--blanc); font-weight: 800; font-size: 1.5rem; letter-spacing: -1px; }
        .logo span { color: var(--granate); }

        .nav-links { display: flex; align-items: center; gap: 30px; }
        .nav-links a {
            color: var(--blanc);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transicion);
        }

        .nav-links a:hover { color: var(--granate); }

        .btn-login {
            background: var(--granate);
            padding: 10px 22px;
            border-radius: 4px;
            transition: var(--transicion) !important;
        }

        .btn-login:hover { transform: scale(1.05); filter: brightness(1.2); }


        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), 
                        url('https://images.unsplash.com/photo-1453728013993-6d66e9c9123a?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--blanc);
            padding: 0 20px;
        }

        .hero h1 { font-size: 4rem; font-weight: 800; margin-bottom: 20px; opacity: 0; transform: translateY(30px); transition: 1s forwards; }
        .hero p { font-size: 1.25rem; max-width: 600px; opacity: 0; transition: 1s 0.3s forwards; }
        .hero.active h1, .hero.active p { opacity: 1; transform: translateY(0); }


        .section { padding: 100px 10%; }
        .title-area { text-align: center; margin-bottom: 60px; }
        .title-area h2 { font-size: 2.5rem; text-transform: uppercase; letter-spacing: 2px; }
        .line { width: 50px; height: 4px; background: var(--granate); margin: 15px auto; }

        .grid-services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .card {
            background: var(--gris-clar);
            padding: 50px 40px;
            border-radius: 12px;
            transition: var(--transicion);
            position: relative;
            overflow: hidden;
        }

        .card:hover { background: var(--blanc); box-shadow: 0 20px 40px rgba(0,0,0,0.05); transform: translateY(-10px); }
        .card h3 { color: var(--granate); margin-bottom: 20px; font-size: 1.5rem; }
        .card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--granate); transform: scaleX(0); transition: var(--transicion); }
        .card:hover::before { transform: scaleX(1); }


        .contact-flex { display: flex; gap: 80px; align-items: center; flex-wrap: wrap; }
        .contact-text { flex: 1; min-width: 300px; }
        .info-item { margin-bottom: 30px; }
        .info-item label { display: block; font-weight: 800; color: var(--granate); text-transform: uppercase; font-size: 0.8rem; margin-bottom: 5px; }
        
        .map-box { 
            flex: 1.2; 
            min-width: 350px; 
            height: 450px; 
            background: #eee; 
            border-radius: 15px; 
            overflow: hidden; 
            box-shadow: 0 30px 60px rgba(0,0,0,0.1); 
        }

        footer { background: var(--negro); color: var(--blanc); padding: 60px 10%; text-align: center; }
        

        .reveal { opacity: 0; transform: translateY(50px); transition: 1s all ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

    </style>
</head>
<body>

    <nav id="navbar">
        <div class="logo">GBA<span>.</span>ADVOS</div>
        <div class="nav-links">
            <a href="#home">Inici</a>
            <a href="#serveis">Serveis</a>
            <a href="#contacte">Contacte</a>
            <a href="src/auth/login.php" class="btn-login">LOGIN</a>
        </div>
    </nav>

    <div id="home" class="hero">
        <h1>Compromís i Justícia</h1>
        <p>L'excel·lència jurídica al servei dels teus interessos. Assessorem amb rigor i visió estratègica.</p>
    </div>

    <section id="serveis" class="section reveal">
        <div class="title-area">
            <h2>Serveis Jurídics</h2>
            <div class="line"></div>
        </div>
        <div class="grid-services">
            <div class="card">
                <h3>Dret Mercantil</h3>
                <p>Constitució de societats, fusions, adquisicions i assessoria integral per a empreses en creixement.</p>
            </div>
            <div class="card">
                <h3>Estratègia Penal</h3>
                <p>Defensa tècnica en procediments complexos, delictes econòmics i compliance corporatiu.</p>
            </div>
            <div class="card">
                <h3>Patrimoni i Família</h3>
                <p>Planificació successòria, divorcis d'alt nivell i gestió d'actius familiars nacionals i internacionals.</p>
            </div>
        </div>
    </section>

    <section id="contacte" class="section reveal" style="background: var(--gris-clar);">
        <div class="title-area">
            <h2>Atenció al Client</h2>
            <div class="line"></div>
        </div>
        <div class="contact-flex">
            <div class="contact-text">
                <div class="info-item">
                    <label>Horaris</label>
                    Dilluns a Dijous: 09h — 18h<br>Divendres: 09h — 15h
                </div>
                <div class="info-item">
                    <label>Adreça</label>
                    Avinguda Diagonal, 450, 2n 1a<br>08006 Barcelona
                </div>
                <div class="info-item">
                    <label>Directe</label>
                    +34 93 123 45 67<br>contacte@gba-advos.cat
                </div>
            </div>
            <div class="map-box">
                <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="Oficina GBA" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 GBA ADVOS. Excellence in Law. Barcelona, Spain.</p>
    </footer>

    <script>

        window.addEventListener('scroll', function() {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });


        window.addEventListener('load', () => {
            document.querySelector('.hero').classList.add('active');
        });


        function reveal() {
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("visible");
                }
            }
        }
        window.addEventListener("scroll", reveal);
    </script>

</body>
</html>
