from num2words import num2words

def numero_a_letras(numero):
    return num2words(numero, lang='es').replace(" y uno", " y un").title()

# Ejemplo de uso
numero = 441767200
texto = numero_a_letras(numero)
print(f"{numero} : {texto}")
