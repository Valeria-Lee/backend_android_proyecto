DROP DATABASE IF EXISTS trackDB;
CREATE DATABASE trackDB;

USE trackDB;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    password VARCHAR(100)
);

CREATE TABLE user_orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    latitude DOUBLE,
    longitude DOUBLE,
    delivered BOOLEAN,
    CONSTRAINT FK_UserOrder FOREIGN KEY (user_id)
    REFERENCES users(user_id)
);

CREATE TABLE qr_orders (
    qr_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    description MEDIUMTEXT,
    url LONGTEXT,
    CONSTRAINT FK_QrOrder FOREIGN KEY (order_id)
    REFERENCES user_orders(order_id)
);

INSERT INTO users (username, password)
    VALUES
    ('usuario', '$2b$12$zYhh19zD7SFSSlyAAWPiN.yXL4ChCOxNrhSFlqILWRi/.8JMCXFg6'), -- track0Rd3n
    ('wicho', '$2b$12$dlTexdc/mhQ/SPkLB2t4VePt4OP2unoQ8WNoq372sy.FmLQ5GM/eO'), -- luis12
    ('fernando', '$2b$12$UMU1mjmuHMfEUr6RypE5c.rhmr8Xx7m3XKVb8H0ISuwXXKo0qY1.2'), -- ferReyes04
    ('raul', '$2b$12$gvtXDc2hUbG4XucIikdH3.uqlQI5F8NUC9IUEtE9h0lUzwgvr3oLq'), -- raul123
    ('valeria', '$2b$12$zI4qY9WYx1o7aYD11KSzdujICDINLkPaSKw1xfv8nh.AFco8tbMjG'); -- 123456


INSERT INTO user_orders (user_id, latitude, longitude, delivered)
    VALUES
    ('1', 22.396428, 114.109497, FALSE), -- Hong Kong
    ('1', 32.5149, -117.0382, FALSE), -- Tijuana, México
    ('1', 19.42847, -99.12766, FALSE), -- Ciudad de México, México
    ('1', 3.1412, 101.68653, FALSE), -- Kuala Lumpur, Malasia
    ('2', 38.328732, -85.764771, FALSE), -- Louisville, E.U.A.
    ('2', 52.377956, 4.897070, FALSE), -- Ámsterdam, Países Bajos
    ('2', 39.9075, 116.397, FALSE), -- Pekín (Beijing), China
    ('3', 32.7767, -96.7970, FALSE), -- Dallas/Fort Worth, E.U.A.
    ('4', 19.4006, -99.0148, FALSE), -- Ciudad Nezahualcóyotl, México
    ('5', 52.377956, 4.897070, FALSE); -- Ámsterdam, Países Bajos

INSERT INTO qr_orders (order_id, description, url)
    VALUES
    (1, 'audifonos-inalambricos-xiaomi-redmi-buds-6-active-white-color-blanco', 'https://www.mercadolibre.com.mx/audifonos-inalambricos-xiaomi-redmi-buds-6-active-white-color-blanco/p/MLM38518568#polycard_client=search-nordic&searchVariation=MLM38518568&wid=MLM2114119417&position=2&search_layout=stack&type=product&tracking_id=2cebc570-83ac-46f3-aa6b-26b97d4184d9&sid=search'),
    (2, 'lenovo-thinkpad-l14-amd-ryzen-3-pro-4450u-16gb-ram-512gb-ssd', 'https://articulo.mercadolibre.com.mx/MLM-3600620242-lenovo-thinkpad-l14-amd-ryzen-3-pro-4450u-16gb-ram-512gb-ssd-_JM?searchVariation=187257698277#polycard_client=search-nordic&searchVariation=187257698277&position=13&search_layout=stack&type=item&tracking_id=b2d5b210-90f4-4c56-899c-042480bf9a5b'),
    (3, 'Minecraft-inteligente-interactivo-pantalla', 'https://www.amazon.com.mx/Minecraft-inteligente-interactivo-pantalla-t%C3%A1ctil/dp/B08YFKKRY3/?_encoding=UTF8&pd_rd_w=egDMI&content-id=amzn1.sym.7f43d914-bf9f-4ad3-94be-b0954a701bb1&pf_rd_p=7f43d914-bf9f-4ad3-94be-b0954a701bb1&pf_rd_r=Q2JTEW0G4Z9B84EKE1CV&pd_rd_wg=j81Bl&pd_rd_r=d92fc6fa-8d86-419e-b209-e0a6a734e1f0&ref_=pd_hp_d_atf_dealz_cs'),
    (4, 'conjunto-de-1-4-piezas-de-licencia-de-conducir-hawaiana-para-fiestas', 'https://www.temu.com/mx/conjunto-de-1-4-piezas-de-licencia-de-conducir-hawaiana-para-fiestas-s--y-aloha-disfraces--para-fiestas-y-festivales-accesorios-para-festivales--divertido-proyectos-duraderos-para-fiestas-adornos-para-fiestas-g-601100374275629.html?_oak_mp_inf=EK3sobKp1ogBGiQxNDlkZjBmMi01YWM5LTQ4ZjEtOGJhYi1lNmJhNzk4YjNiNzggsPm%2Fjewy&top_gallery_url=https%3A%2F%2Fimg.kwcdn.com%2Fproduct%2Ffancy%2F945da8f2-c8cd-45f3-b6c1-211687a268fe.jpg&spec_gallery_id=5199540690&refer_page_sn=10132&refer_source=0&freesia_scene=311&_oak_freesia_scene=311&_oak_rec_ext_1=MTQyMg&_oak_gallery_order=439344921%2C262756327%2C968340116%2C1431355585%2C638097155&refer_page_el_sn=207153&_x_channel_src=1&_x_channel_scene=spike&_x_sessn_id=5aidn7l3ya&refer_page_name=lightning-deals&refer_page_id=10132_1747006259808_2hi0pbaq68'),
    (5, 'Minecraft-Enciclopedia-mobs-Mojang', 'https://www.amazon.com.mx/Minecraft-Enciclopedia-mobs-Mojang-Ab/dp/8410021064/?_encoding=UTF8&pd_rd_w=dNFfT&content-id=amzn1.sym.7f43d914-bf9f-4ad3-94be-b0954a701bb1&pf_rd_p=7f43d914-bf9f-4ad3-94be-b0954a701bb1&pf_rd_r=NYK3BX2JDXGHJW9RWYH9&pd_rd_wg=K4Dxo&pd_rd_r=a2e62b1f-8b6e-4b92-873b-ce4d7e1a3322&ref_=pd_hp_d_atf_dealz_cs'),
    (6, 'ventilador-con-aspas-de-metal-gutstark', 'https://www.walmart.com.mx/ip/aire-acondicionado-y-calentadores/ventilador-con-aspas-de-metal-gutstark-3-en-1-pedestal-18-soporte-base-1-3-mt-60-w/00750227963263?athcpid=00750227963263&athpgid=ContentPage&athcgid=null&athznid=ItemCarousel_25f9530d-5e64-45df-9ec6-e261cac83fb2_items&athieid=v0&athstid=CS020&athguid=Q39eBqC1eKGAfEw38258wH_ZzDIOYMq24Bp7&athancid=null&athena=true&athbdg=L1300'),
    (7, 'item/1005008884914675', 'https://es.aliexpress.com/item/1005008884914675.html?spm=a2g0o.productlist.main.5.6cda9198J47JtO&algo_pvid=83aacb9b-244c-4170-84fc-d437a71e1b26&algo_exp_id=83aacb9b-244c-4170-84fc-d437a71e1b26-4&pdp_ext_f=%7B%22order%22%3A%2234%22%2C%22eval%22%3A%221%22%7D&pdp_npi=4%40dis%21MXN%211175.65%21730.71%21%21%21427.12%21265.47%21%402101c5bf17470064054915742ef64d%2112000047079940739%21sea%21MX%210%21ABX&curPageLogUid=T1nDVFlukse3&utparam-url=scene%3Asearch%7Cquery_from%3A'),
    (8, 'Sabritas-Cheetos-Xtra-Flamin-240g', 'https://www.amazon.com.mx/Sabritas-Cheetos-Xtra-Flamin-240g/dp/B0BNL253VL/ref=sr_1_1?__mk_es_MX=%C3%85M%C3%85%C5%BD%C3%95%C3%91&dib=eyJ2IjoiMSJ9.WxJFwgRQCsQl534YbnwfA5ABNTpstTyq__JQO8Sz3PgVRjFyFDiyEniKKzBj5uL-tezsAxQ9sgBq6F5phKwOGfD7DTgRN0kw8qEOR9I62aofIpmk7k-cO9XegqH1rdFLSJZdMVlFtAe-w1CdIPCYzVgISbOwRnwDKOATczt_oYQMY_BUddz8hy4E7IkHOPlWQgJ79E-U0GPsAsZlLWiOAl_S4A1vB3_KgmvYB8I_JCfV-UAjA9Om96TyMRBkGiW_NeOuqFZ8_wCwtwhGHiGyt48lBH3chTAEZmHRSljpcpQ.Me_-809d5n986cOKikQHaTBQ21T8HrT0aaQArQcqi7s&dib_tag=se&keywords=cheetos&qid=1747006397&s=grocery&sr=1-1'),
    (9, 'JBL-Port', 'https://www.amazon.com.mx/JBL-Port%C3%A1til-Bluetooth-Reproducci%C3%B3n-Resistente/dp/B0CTNWBT1Z?ref=dlx_deals_dg_dcl_B0CTNWBT1Z_dt_sl14_6c_pi&pf_rd_r=YRDKRP6H3VSCE5102AJC&pf_rd_p=95f51032-b2e2-4dbc-9604-3b494fd45a6c&th=1'),
    (10, 'LG-Monitor-29WQ500-Pulgadas-Contrast', 'https://www.amazon.com.mx/LG-Monitor-29WQ500-Pulgadas-Contrast/dp/B0C3WZ4NNJ?ref=dlx_deals_dg_dcl_B0C3WZ4NNJ_dt_sl14_6c_pi&pf_rd_r=TDDQATY63AM1M83CVGDA&pf_rd_p=95f51032-b2e2-4dbc-9604-3b494fd45a6c');