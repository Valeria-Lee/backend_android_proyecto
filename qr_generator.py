import segno

productos = [
    'audifonos-inalambricos-xiaomi-redmi-buds-6-active-white-color-blanco',
    'lenovo-thinkpad-l14-amd-ryzen-3-pro-4450u-16gb-ram-512gb-ssd',
    'Minecraft-inteligente-interactivo-pantalla',
    'conjunto-de-1-4-piezas-de-licencia-de-conducir-hawaiana-para-fiestas',
    'Minecraft-Enciclopedia-mobs-Mojang',
    'ventilador-con-aspas-de-metal-gutstark',
    'item/1005008884914675',
    'Sabritas-Cheetos-Xtra-Flamin-240g',
    'JBL-Port',
    'LG-Monitor-29WQ500-Pulgadas-Contrast'
]

i = 0

for producto in productos:
    qrcode = segno.make_qr(producto)
    qrcode.save(f"{i}_producto.png", scale=5)
    i+=1