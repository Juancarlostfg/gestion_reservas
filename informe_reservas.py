import mysql.connector
from reportlab.lib.pagesizes import A4
from reportlab.pdfgen import canvas
from datetime import datetime

# === CONFIGURACIÓN BASE DE DATOS ===
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",  # si tienes clave en MySQL, ponla aquí
    "database": "gestion_reservas",
}


def obtener_datos():
    """Se conecta a MySQL y devuelve varios conjuntos de datos para el informe."""
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor(dictionary=True)

    # Total de reservas
    cursor.execute("SELECT COUNT(*) AS total_reservas FROM reservas")
    total_reservas = cursor.fetchone()["total_reservas"]

    # Total de ingresos
    cursor.execute("SELECT COALESCE(SUM(precio_total),0) AS ingresos_totales FROM reservas")
    ingresos_totales = cursor.fetchone()["ingresos_totales"]

    # Reservas por estado
    cursor.execute("""
        SELECT estado_reserva, COUNT(*) AS num_reservas,
               COALESCE(SUM(precio_total),0) AS ingresos
        FROM reservas
        GROUP BY estado_reserva
    """)
    por_estado = cursor.fetchall()

    # Reservas por tipo de vehículo
    cursor.execute("""
        SELECT tipo_vehiculo, COUNT(*) AS num_reservas,
               COALESCE(SUM(precio_total),0) AS ingresos
        FROM reservas
        GROUP BY tipo_vehiculo
    """)
    por_tipo = cursor.fetchall()

    # Reservas por mes (según fecha_inicio)
    cursor.execute("""
        SELECT DATE_FORMAT(fecha_inicio, '%Y-%m') AS mes,
               COUNT(*) AS num_reservas,
               COALESCE(SUM(precio_total),0) AS ingresos
        FROM reservas
        GROUP BY mes
        ORDER BY mes
    """)
    por_mes = cursor.fetchall()

    cursor.close()
    conn.close()

    return {
        "total_reservas": total_reservas,
        "ingresos_totales": ingresos_totales,
        "por_estado": por_estado,
        "por_tipo": por_tipo,
        "por_mes": por_mes,
    }


def dibujar_titulo(c, texto, y):
    c.setFont("Helvetica-Bold", 18)
    c.drawString(50, y, texto)
    return y - 30


def dibujar_subtitulo(c, texto, y):
    c.setFont("Helvetica-Bold", 14)
    c.drawString(50, y, texto)
    return y - 22


def dibujar_linea_texto(c, texto, y):
    c.setFont("Helvetica", 11)
    c.drawString(60, y, texto)
    return y - 16


def nueva_pagina_si_hace_falta(c, y):
    """Si nos quedamos sin espacio, crea nueva página y resetea Y."""
    if y < 80:
        c.showPage()
        y = 800
    return y


def generar_pdf(datos, nombre_fichero="informe_reservas.pdf"):
    c = canvas.Canvas(nombre_fichero, pagesize=A4)
    width, height = A4

    # Cabecera
    y = 800
    c.setFont("Helvetica-Bold", 20)
    c.drawString(50, y, "Informe de Reservas - Autos Costa Sol")

    c.setFont("Helvetica", 10)
    fecha_str = datetime.now().strftime("%d/%m/%Y %H:%M")
    c.drawRightString(width - 40, y, f"Generado: {fecha_str}")
    y -= 40

    # Resumen general
    y = dibujar_titulo(c, "1. Resumen general", y)

    y = dibujar_linea_texto(
        c,
        f"Total de reservas registradas: {datos['total_reservas']}",
        y
    )
    y = dibujar_linea_texto(
        c,
        f"Ingresos totales (todas las reservas): {datos['ingresos_totales']:.2f} €",
        y
    )
    y -= 10
    y = nueva_pagina_si_hace_falta(c, y)

    # Reservas por estado
    y = dibujar_titulo(c, "2. Reservas por estado", y)

    if datos["por_estado"]:
        for fila in datos["por_estado"]:
            linea = (
                f"- {fila['estado_reserva']}: "
                f"{fila['num_reservas']} reservas "
                f"({fila['ingresos']:.2f} €)"
            )
            y = dibujar_linea_texto(c, linea, y)
            y = nueva_pagina_si_hace_falta(c, y)
    else:
        y = dibujar_linea_texto(c, "No hay datos de reservas por estado.", y)
        y = nueva_pagina_si_hace_falta(c, y)

    y -= 10

    # Reservas por tipo de vehículo
    y = dibujar_titulo(c, "3. Reservas por tipo de vehículo", y)

    if datos["por_tipo"]:
        for fila in datos["por_tipo"]:
            linea = (
                f"- {fila['tipo_vehiculo']}: "
                f"{fila['num_reservas']} reservas "
                f"({fila['ingresos']:.2f} €)"
            )
            y = dibujar_linea_texto(c, linea, y)
            y = nueva_pagina_si_hace_falta(c, y)
    else:
        y = dibujar_linea_texto(c, "No hay datos de reservas por tipo.", y)
        y = nueva_pagina_si_hace_falta(c, y)

    y -= 10

    # Reservas por mes
    y = dibujar_titulo(c, "4. Evolución por mes (según fecha de inicio)", y)

    if datos["por_mes"]:
        for fila in datos["por_mes"]:
            mes = fila["mes"] or "Sin fecha"
            linea = (
                f"- {mes}: {fila['num_reservas']} reservas "
                f"({fila['ingresos']:.2f} €)"
            )
            y = dibujar_linea_texto(c, linea, y)
            y = nueva_pagina_si_hace_falta(c, y)
    else:
        y = dibujar_linea_texto(c, "No hay datos agrupados por mes.", y)
        y = nueva_pagina_si_hace_falta(c, y)

    # Pie
    y -= 20
    c.setFont("Helvetica-Oblique", 9)
    c.drawString(50, y, "Informe generado automáticamente desde la base de datos gestion_reservas.")

    c.showPage()
    c.save()


def main():
 def main():
    print("========================================")
    print("  INFORME DE RESERVAS - AUTOS COSTA SOL ")
    print("========================================")

    try:
        import sys
        # Si se ejecuta en consola interactiva, pedimos ENTER.
        if sys.stdin.isatty():
            input("Pulsa ENTER para generar el informe PDF...")

        datos = obtener_datos()
        generar_pdf(datos)
        print("\n✅ Informe generado correctamente: informe_reservas.pdf")
    except Exception as e:
        print("\n❌ Error al generar el informe:")
        print(e)

    main()

