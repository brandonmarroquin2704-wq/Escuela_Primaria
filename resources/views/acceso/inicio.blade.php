<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar</title>

    <style>
        :root {
            --azul: #1d4ed8;
            --azul-oscuro: #1e3a8a;
            --fondo: #f1f5f9;
            --texto: #1e293b;
            --borde: #dbeafe;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            background: linear-gradient(135deg, #eff6ff, var(--fondo));
            color: var(--texto);
            font-family: Arial, Helvetica, sans-serif;
        }

        .contenedor {
            width: min(100%, 880px);
            padding: 48px;
            background: #ffffff;
            border: 1px solid var(--borde);
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(30, 58, 138, 0.12);
            text-align: center;
        }

        h1 {
            margin: 0;
            color: var(--azul-oscuro);
            font-size: clamp(1.8rem, 4vw, 2.6rem);
        }

        .descripcion {
            max-width: 600px;
            margin: 14px auto 36px;
            color: #475569;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .opciones {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .tarjeta {
            display: flex;
            min-height: 230px;
            padding: 30px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #bfdbfe;
            border-radius: 16px;
            background: #f8fbff;
        }

        .icono {
            width: 62px;
            height: 62px;
            display: grid;
            place-items: center;
            margin-bottom: 16px;
            border-radius: 50%;
            background: #dbeafe;
            color: var(--azul-oscuro);
            font-size: 1.7rem;
        }

        .tarjeta h2 {
            margin: 0;
            color: var(--azul-oscuro);
            font-size: 1.35rem;
        }

        .tarjeta p {
            min-height: 48px;
            margin: 12px 0 20px;
            color: #475569;
            line-height: 1.5;
        }

        .boton {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 8px;
            background: var(--azul);
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            transition: background .2s ease, transform .2s ease;
        }

        .boton:hover {
            background: var(--azul-oscuro);
            transform: translateY(-2px);
        }

        .nota {
            margin: 28px 0 0;
            color: #64748b;
            font-size: .92rem;
        }

        @media (max-width: 640px) {
            .contenedor {
                padding: 32px 22px;
            }

            .opciones {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="contenedor">
        <h1>Escuela Primaria 21 De Octubre</h1>

        <p class="descripcion">
            Selecciona el tipo de acceso que corresponde a tu perfil.
        </p>

        <section class="opciones">
            <article class="tarjeta">
                <div class="icono">👨‍👩‍👧</div>

                <h2>Soy padre o tutor</h2>

                <p>
                    Consulta el estado de tus solicitudes y la información
                    autorizada de tus hijos.
                </p>

                <a class="boton" href="{{ route('login') }}">
                    Acceder como padre o tutor
                </a>
            </article>

            <article class="tarjeta">
                <div class="icono">🏫</div>

                <h2>Soy personal de la escuela</h2>

                <p>
                    Accede con la cuenta institucional asignada por la
                    administración escolar.
                </p>

                <a class="boton" href="{{ route('login') }}">
                    Acceder como personal
                </a>
            </article>
        </section>

        <p class="nota">
            Las cuentas del personal son creadas y administradas por la escuela.
        </p>
    </main>
</body>
</html>