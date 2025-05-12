import bcrypt

'''

Para guardar las contrasenas en el mysql, necesitamos encriptarlas por seguridad, incluso si la inserta de datos es en el mismo script.

Se necesita descargar la libreria bcrypt, pueden descargarla con:
    sudo apt install python3-bcrypt
o en su virtual environment con:
    pip install bcrypt

'''
password = (input("Inserta la contrasena a insertar: ")).encode('utf-8')
hashed_pass = bcrypt.hashpw(password, bcrypt.gensalt())

print("Esta es la pass que anadiras a tu inserta de valores: ", end='\n')
print(hashed_pass.decode())
