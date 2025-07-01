from graphviz import Digraph


# Crear el objeto del diagrama mejorado
flowchart = Digraph(format='pdf', name="Diagrama_Flujo_Lavanderia_Condicionales")

# Configuración general
flowchart.attr(rankdir='TB', size='10')
flowchart.attr('node', shape='rectangle', style='rounded, filled', color='lightblue', fontname='Arial')

# Nodos del diagrama
flowchart.node("inicio", "Inicio\nCliente llega o hace pedido")
flowchart.node("seleccion", "Selecciona servicio:\nLavado, Secado, Planchado o Combinado")
flowchart.node("recepcion", "Recepción de ropa y etiquetado")
flowchart.node("clasificacion", "Clasificación por tela y color")
flowchart.node("lavado", "Lavado\nInsumos: Detergente, Agua")
flowchart.node("secado", "Secado\nInsumos: Secadora, Energía")
flowchart.node("planchado", "Planchado\nInsumos: Plancha, Energía")
flowchart.node("combinado", "Combinado\nInsumos: Detergente, Secadora, Plancha")
flowchart.node("control_calidad", "Control de calidad y empaquetado")
flowchart.node("notificacion", "Notificación al cliente")
flowchart.node("entrega", "Entrega de ropa")

# Relaciones (flechas)
flowchart.edge("inicio", "seleccion")
flowchart.edge("seleccion", "recepcion")
flowchart.edge("recepcion", "clasificacion")
flowchart.edge("clasificacion", "lavado", label="Lavado")
flowchart.edge("clasificacion", "secado", label="Secado")
flowchart.edge("clasificacion", "planchado", label="Planchado")
flowchart.edge("clasificacion", "combinado", label="Combinado")
flowchart.edge("lavado", "control_calidad")
flowchart.edge("secado", "control_calidad")
flowchart.edge("planchado", "control_calidad")
flowchart.edge("combinado", "control_calidad")
flowchart.edge("control_calidad", "notificacion")
flowchart.edge("notificacion", "entrega")



# Exportar a una ruta accesible
output_path = "./Diagrama_Flujo_LavanderiaAA"  # Cambia según tu nombre de usuario
flowchart.render(output_path, view=False)

# Renderizar el diagrama mejorado a un archivo PDF
# flowchart.render("/mnt/data/Diagrama_Flujo_Lavanderia_Condicionales")

print(f"Diagrama generado y guardado en: {output_path}.pdf")
