def calcular_roi(gastos_operativos, inversion_inicial, ganancias_servicios, clientes_por_mes):
    # Costos fijos mensuales (sumar todos los valores de los gastos operativos)
    costos_fijos = sum(gastos_operativos.values())
    
    # Inversión inicial (sumar todos los valores de la inversión inicial)
    inversion_inicial_total = sum(inversion_inicial.values())
    
    # Ingresos por cliente (suponiendo 2 kg de ropa y 5 prendas para planchar)
    ingresos_cliente = (ganancias_servicios['precio_lavado'] * 2) + (ganancias_servicios['precio_planchado'] * 5)
    
    # Ingresos mensuales
    ingresos_mensuales = ingresos_cliente * clientes_por_mes
    
    # Beneficio neto mensual
    beneficio_neto = ingresos_mensuales - costos_fijos
    
    # ROI
    roi = (beneficio_neto / inversion_inicial_total) * 100
    
    # Tiempo de recuperación
    if beneficio_neto > 0:
        tiempo_recuperacion = inversion_inicial_total / beneficio_neto
    else:
        tiempo_recuperacion = float('inf')  # Si no hay beneficio neto, no hay recuperación
    
    # Margen de beneficio neto
    margen_beneficio_neto = (beneficio_neto / ingresos_mensuales) * 100
    
    # Punto de equilibrio (clientes necesarios para cubrir costos fijos)
    punto_equilibrio = costos_fijos / (ingresos_cliente) if ingresos_cliente > 0 else float('inf')
    
    # Utilización de la capacidad (suponiendo que hay una capacidad máxima de clientes, por ejemplo, 500)
    capacidad_maxima = 500  # Capacidad máxima de clientes que se pueden atender al mes
    utilizacion_capacidad = (clientes_por_mes / capacidad_maxima) * 100 if capacidad_maxima > 0 else 0
    
    # Determinar si es viable
    if roi >= 5:
        viabilidad = "Viable"
    else:
        viabilidad = "Riesgoso"
    
    return roi, tiempo_recuperacion, viabilidad, margen_beneficio_neto, punto_equilibrio, utilizacion_capacidad

# Ingresar los valores en diccionarios
Lista_Gastos_Operativos = {
    'renta': 6500,
    'agua': 3500,
    'luz': 4500,
    'gas': 1000,
    'rh': 6800
}

Lista_Inversion_Inicial = {
    'lavadoras_secadoras': 38000,
    'planchas': 3000,
    'detergente': 500,
    'suavitel': 500,
    'inmobilario': 10000
}

Lista_Ganancias_Servicios = {
    'precio_lavado': 15,
    'precio_planchado': 10
}

clientes_por_mes = 300

# Calcular ROI y viabilidad
roi, tiempo_recuperacion, viabilidad, margen_beneficio_neto, punto_equilibrio, utilizacion_capacidad = calcular_roi(Lista_Gastos_Operativos, Lista_Inversion_Inicial, Lista_Ganancias_Servicios, clientes_por_mes)

# Mostrar resultados
print(f"ROI mensual: {roi:.2f}%")
print(f"Tiempo de recuperación de la inversión: {tiempo_recuperacion:.2f} meses")
print(f"Viabilidad del negocio: {viabilidad}")
print(f"Margen de beneficio neto: {margen_beneficio_neto:.2f}%")
print(f"Punto de equilibrio (clientes): {punto_equilibrio:.2f} clientes")
print(f"Utilización de la capacidad: {utilizacion_capacidad:.2f}%")

## #################################################################### ##
## ####################### SOFTWARE AVANZADO ########################## ##
## #################################################################### ##

# def calcular_roi(gastos_operativos, inversion_inicial, ganancias_servicios, clientes_por_mes, gastos_marketing, clientes_anteriores, ingresos_generados_por_marketing, nuevos_clientes):
#     # Costos fijos mensuales (sumar todos los valores de los gastos operativos)
#     costos_fijos = sum(gastos_operativos.values())
    
#     # Inversión inicial (sumar todos los valores de la inversión inicial)
#     inversion_inicial_total = sum(inversion_inicial.values())
    
#     # Ingresos por cliente (suponiendo 2 kg de ropa y 5 prendas para planchar)
#     ingresos_cliente = (ganancias_servicios['precio_lavado'] * 2) + (ganancias_servicios['precio_planchado'] * 5)
    
#     # Ingresos mensuales
#     ingresos_mensuales = ingresos_cliente * clientes_por_mes
    
#     # Beneficio neto mensual
#     beneficio_neto = ingresos_mensuales - costos_fijos
    
#     # ROI
#     roi = (beneficio_neto / inversion_inicial_total) * 100
    
#     # Tasa de crecimiento mensual del ROI (supone que tienes el ROI del mes anterior)
#     roi_anterior = 3.0  # Esto debe ser modificado con el ROI real del mes anterior
#     tasa_crecimiento_roi = ((roi - roi_anterior) / roi_anterior) * 100 if roi_anterior != 0 else 0
    
#     # Costo por adquisición de cliente (CAC)
#     cac = gastos_marketing / nuevos_clientes if nuevos_clientes != 0 else 0
    
#     # Valor del tiempo de vida del cliente (CLTV)
#     valor_medio_compra = ingresos_cliente  # Ingreso promedio por cliente
#     frecuencia_compra = 2  # Supón que un cliente compra 2 veces al mes
#     duracion_media_cliente = 12  # Supón que la duración media es de 12 meses
#     cltv = valor_medio_compra * frecuencia_compra * duracion_media_cliente
    
#     # Retorno sobre el gasto en marketing (ROMI)
#     romi = (ingresos_generados_por_marketing - gastos_marketing) / gastos_marketing * 100 if gastos_marketing != 0 else 0
    
#     # Tasa de retención de clientes
#     clientes_iniciales = clientes_anteriores  # Clientes al principio del mes
#     clientes_finales = clientes_por_mes  # Clientes al final del mes
#     tasa_retencion = ((clientes_finales - nuevos_clientes) / clientes_iniciales) * 100 if clientes_iniciales != 0 else 0
    
#     # Determinar si es viable
#     viabilidad = "Viable" if roi >= 5 else "Riesgoso"
    
#     # Cálculos adicionales
#     tiempo_recuperacion_inversion = inversion_inicial_total / beneficio_neto if beneficio_neto != 0 else 0
#     margen_beneficio_neto = (beneficio_neto / ingresos_mensuales) * 100 if ingresos_mensuales != 0 else 0
#     punto_equilibrio = costos_fijos / (ingresos_cliente - costos_fijos / clientes_por_mes) if ingresos_cliente - costos_fijos / clientes_por_mes != 0 else 0
#     utilizacion_capacidad = (clientes_por_mes / 500) * 100  # Suponiendo que la capacidad máxima es de 500 clientes
    
#     return roi, tasa_crecimiento_roi, cac, cltv, romi, tasa_retencion, viabilidad, tiempo_recuperacion_inversion, margen_beneficio_neto, punto_equilibrio, utilizacion_capacidad

# # Ingresar los valores
# Lista_Gastos_Operativos = {
#     'renta': 6500,
#     'agua': 3500,
#     'luz': 4500,
#     'gas': 1000,
#     'rh': 6800
# }

# Lista_Inversion_Inicial = {
#     'lavadoras_secadoras': 38000,
#     'planchas': 3000,
#     'detergente': 500,
#     'suavitel': 500,
#     'inmobilario': 10000
# }

# Lista_Ganancias_Servicios = {
#     'precio_lavado': 15,
#     'precio_planchado': 10
# }

# clientes_por_mes = 150
# gastos_marketing = 100  # Ejemplo de gastos en marketing
# clientes_anteriores = 5  # Clientes del mes anterior
# ingresos_generados_por_marketing = 200  # Ingresos generados directamente por marketing
# nuevos_clientes = 5  # Número de nuevos clientes adquiridos

# # Calcular ROI y viabilidad
# roi, tasa_crecimiento_roi, cac, cltv, romi, tasa_retencion, viabilidad, tiempo_recuperacion_inversion, margen_beneficio_neto, punto_equilibrio, utilizacion_capacidad = calcular_roi(Lista_Gastos_Operativos, Lista_Inversion_Inicial, Lista_Ganancias_Servicios, clientes_por_mes, gastos_marketing, clientes_anteriores, ingresos_generados_por_marketing, nuevos_clientes)

# # Mostrar resultados
# print(f"ROI mensual: {roi:.2f}%")
# print(f"Tasa de crecimiento mensual del ROI: {tasa_crecimiento_roi:.2f}%")
# print(f"Costo por adquisición de cliente (CAC): {cac:.2f}")
# print(f"Valor del tiempo de vida del cliente (CLTV): {cltv:.2f}")
# print(f"Retorno sobre el gasto en marketing (ROMI): {romi:.2f}%")
# print(f"Tasa de retención de clientes: {tasa_retencion:.2f}%")
# print(f"Viabilidad del negocio: {viabilidad}")
# print(f"Margen de beneficio neto: {margen_beneficio_neto:.2f}%")
# print(f"Punto de equilibrio (clientes): {punto_equilibrio:.2f} clientes")
# print(f"Utilización de la capacidad: {utilizacion_capacidad:.2f}%")
# print(f"Tiempo de recuperación de la inversión: {tiempo_recuperacion_inversion:.2f} meses")








# 1. Viabilidad: Si el ROI es mayor o igual al 5%, el negocio se considera "viable". Si es menor, se considera "riesgoso".
    # calcular_roi: Función que toma todos los valores de los 7 puntos y calcula:
    # Los costos fijos mensuales.
    # La inversión inicial.
    # Los ingresos por cliente.
    # Los ingresos mensuales.
    # El beneficio neto mensual.
    # El ROI.
    # El tiempo de recuperación de la inversión.
    # Viabilidad: Si el ROI es mayor o igual al 5%, el negocio se considera "viable". Si es menor, se considera "riesgoso".
# 2. Tasa de crecimiento mensual del ROI: Este KPI mide cómo cambia tu ROI mes a mes. Te da una idea de si tu rentabilidad está mejorando o empeorando con el tiempo.
    # Variables necesarias:
    # ROI del mes anterior (puedes almacenar este valor para comparar con el ROI actual
# 3. Costo por adquisición de cliente (CAC): Este KPI calcula cuánto te cuesta adquirir un cliente, sumando los gastos de marketing, ventas y otros gastos relacionados con la captación de nuevos clientes.
    # Variables necesarias:
    # Gastos en marketing/ventas (por ejemplo, dinero invertido en publicidad).
    # Número de nuevos clientes adquiridos en el mes
# 4. Valor del tiempo de vida del cliente (CLTV): Este KPI calcula cuánto vale un cliente a lo largo de su relación con tu negocio. Ayuda a determinar si el costo de adquisición es justificable en función de lo que cada cliente genera.
    # Variables necesarias:
    # Valor medio de compra por cliente (puedes calcularlo sumando los ingresos por cliente en un periodo de tiempo).
    # Frecuencia de compra mensual (cuántas veces compra un cliente al mes).
    # Duración media de la relación con el cliente (cuánto tiempo, en meses, un cliente permanece activo).
# 5. Retorno sobre el gasto en marketing (ROMI): Este KPI mide cuánto retorno generas por cada unidad de dinero invertido en marketing.
    # Variables necesarias:
    # Ingresos generados por marketing (por ejemplo, ingresos atribuibles directamente a una campaña publicitaria).
    # Gastos de marketing.
# 6. Tasa de retención de clientes: Este KPI mide qué tan bien estás reteniendo a tus clientes. Es especialmente importante para negocios basados en suscripciones o relaciones continuas.
    # Variables necesarias:
    # Clientes al principio del mes.
    # Clientes al final del mes.
    # Nuevos clientes adquiridos durante el mes.
# 7. Margen de beneficio neto: Este KPI muestra el porcentaje de beneficio neto sobre los ingresos, lo que te ayuda a entender qué tan eficiente es tu operación.
# 8. Punto de equilibrio: Este KPI te indica el número de clientes o ingresos necesarios para cubrir tus costos fijos e inversión inicial, sin tener pérdidas ni ganancias.
# 9. Utilización de la capacidad: Si tu negocio tiene un límite en la cantidad de clientes que puedes atender (por ejemplo, las lavadoras o planchas que tienes), este KPI te puede ayudar a saber qué tan cerca estás de tu capacidad máxima.
