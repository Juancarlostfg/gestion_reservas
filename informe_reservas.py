import mysql.connector
from reportlab.lib.pagesizes import A4
from reportlab.pdfgen import canvas
from datetime import datetime
import os
import sys

# === CONFIGURACIÓN BASE DE DATOS ===
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "gestion_reservas",
}

# === RUTA ABSOLUTA DEL PDF ===
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
PDF_PATH = os.path.join(BASE_DIR, "informe_reservas.pdf")


def obtener_datos():
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor(dictionary=True)

    cursor.execute("SELECT COUNT(*) AS total FROM reservas")
    total_reservas = cursor.fetchone()["total"]

    cursor.execute("SELECT COALESCE(SUM(precio_total),0) AS total FROM reservas")
    ingresos_totales = cursor.fetchone()["total"]

    cursor.execute("""
        SELECT estado_reserva, COUNT(*) AS num, COALESCE(SUM(precio_total),0) AS ingresos
        FROM reservas
        GROUP BY estado_reserva
    """)
    por_estado = cursor.fetchall()

    cursor.execute("""
        SELECT tipo_vehiculo, COUNT(*) AS num, COALESCE(SUM(precio_total),0) AS ingresos
        FROM reservas
        GROUP BY tipo_vehiculo
    """)
    por_tipo = cursor.fetchall()

    cursor.execute("""
        SELECT DATE_FORMAT(fecha_inicio, '%Y-%m') AS mes,
               COUNT(*) AS num,
               COALESCE(SUM(precio_total),0) AS ingresos
        FROM reservas
        GROUP BY mes
        ORDER BY mes
    """)
    por_mes = cursor.fetchall()

    cursor.close()
    conn.close()

    return {
        "total": total_reservas,
        "ingresos": ingresos_totales,
        "estado": por_estado,
        "tipo": por_tipo,
        "mes": por_mes,
    }


def generar_pdf(datos):
    c = canvas.Canvas(PDF_PATH, pagesize=A4)
    width, height = A4
    y = height - 50

    c.setFont("Helvetica-Bold", 20)
    c.drawString(50, y, "Informe de Reservas - Autos Costa Sol")
    c.setFont("Helvetica", 10)
    c.drawRightString(width - 40, y, datetime.now().strftime("%d/%m/%Y %H:%M"))
    y -= 40

    c.setFont("Helvetica-Bold", 14)
    c.drawString(50, y, "Resumen general")
    y -= 20

    c.setFont("Helvetica", 11)
    c.drawString(60, y, f"Total de reservas: {datos['total']}")
    y -= 16
    c.drawString(60, y, f"Ingresos totales: {datos['ingresos']:.2f} €")
    y -= 30

    c.setFont("Helvetica-Bold", 14)
    c.drawString(50, y, "Reservas por estado")
    y -= 20

    c.setFont("Helvetica", 11)
    for r in datos["estado"]:
        c.drawString(60, y, f"- {r['estado_reserva']}: {r['num']} ({r['ingresos']:.2f} €)")
        y -= 16

    y -= 20
    c.setFont("Helvetica-Bold", 14)
    c.drawString(50, y, "Reservas por tipo de vehículo")
    y -= 20

    c.setFont("Helvetica", 11)
    for r in datos["tipo"]:
        c.drawString(60, y, f"- {r['tipo_vehiculo']}: {r['num']} ({r['ingresos']:.2f} €)")
        y -= 16

    y -= 20
    c.setFont("Helvetica-Bold", 14)
    c.drawString(50, y, "Reservas por mes")
    y -= 20

    c.setFont("Helvetica", 11)
    for r in datos["mes"]:
        c.drawString(60, y, f"- {r['mes']}: {r['num']} ({r['ingresos']:.2f} €)")
        y -= 16

    c.save()


def main():
    try:
        datos = obtener_datos()
        generar_pdf(datos)
        print("OK: informe generado")
    except Exception as e:
        print("ERROR:", e)
        sys.exit(1)


if __name__ == "__main__":
    main()
