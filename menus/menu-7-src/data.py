# Menu data for Prusakovų užeiga, taken from the POS (categories, subcategories,
# numbers and prices as of 2026-10-10). Every text is {lt, ru, en}.

from pl import PL

LANGS = ["lt", "ru", "en", "pl"]


def t(lt, ru, en):
    """Polish is looked up by the English text, so a missing translation fails loudly."""
    return {"lt": lt, "ru": ru, "en": en, "pl": PL[en]}


def item(number, name, price, desc=None, vol=None, half=None, n18=False):
    return {"n": number, "name": name, "price": price, "desc": desc, "vol": vol, "half": half, "n18": n18}


UI = {
    "brand": t("Prusakovų užeiga", "Prusakovų užeiga", "Prusakovų užeiga"),
    "menu": t("Meniu", "Меню", "Menu"),
    "lang_name": t("Lietuvių", "Русский", "English"),
    "half": t("½ porcija", "½ порции", "½ portion"),
    "full": t("Pilna", "Полная", "Full"),
    "included": t("įskaičiuota", "включено", "included"),
    "packaging": t("Pakuotės mokestis įskaičiuotas į kainą.", "Стоимость упаковки включена в цену.", "Packaging is included in the price."),
    "n18": t("N18 — parduodama tik asmenims nuo 18 metų.", "18+ — продаётся только лицам старше 18 лет.", "18+ — sold only to persons aged 18 and over."),
    "enjoy": t("Skanaus!", "Приятного аппетита!", "Enjoy your meal!"),
    "pdf": t("PDF", "PDF", "PDF"),
    "addons_note": t("Pasirinkite prie čebureko", "На выбор к чебуреку", "Add to any cheburek"),
    "koldunai_addons_note": t("Pasirinkite prie patiekalo", "На выбор к блюду", "Add to any dish"),
    "potato_sauce_note": t("Prie kiekvieno patiekalo", "К каждому блюду", "With every dish"),
    "page": t("Puslapis", "Страница", "Page"),
}

ML = t("ml", "мл", "ml")

CATEGORIES = [
    {
        "id": "ceburekai",
        "page": 1,
        "title": t("Čeburekai", "Чебуреки", "Chebureki"),
        "groups": [
            {"title": t("Su sūriu", "С сыром", "With cheese"), "items": [
                item(1, t("Sūris + grybai", "Сыр + грибы", "Cheese & mushrooms"), 5.50),
                item(2, t("2-jų rūšių sūris", "Два вида сыра", "Two-cheese blend"), 5.50),
                item(3, t("Saliamis + sūris", "Салями + сыр", "Salami & cheese"), 5.50),
            ]},
            {"title": t("Su mėsa", "С мясом", "With meat"), "items": [
                item(1, t("Kiauliena", "Свинина", "Pork"), 5.50),
                item(2, t("Vištiena", "Курица", "Chicken"), 5.90),
                item(3, t("Mix", "Микс", "Mix"), 5.90, desc=t("kiauliena + jautiena", "свинина + говядина", "pork + beef")),
                item(4, t("Jautiena", "Говядина", "Beef"), 6.50),
                item(5, t("Maišytas", "Смешанный", "Mixed"), 6.90, desc=t("jautiena + aviena", "говядина + баранина", "beef + lamb")),
                item(6, t("Aviena", "Баранина", "Lamb"), 7.20),
            ]},
            {"title": t("Firminiai", "Фирменные", "Signature"), "items": [
                item(1, t("Šefo", "От шефа", "Chef's special"), 7.50,
                     desc=t("kiauliena, jautiena, grybai, jalapenai, 2 rūšių sūris",
                            "свинина, говядина, грибы, халапеньо, два вида сыра",
                            "pork, beef, mushrooms, jalapeños, two-cheese blend")),
                item(2, t("Gurmaniškas", "Гурманский", "Gourmet"), 7.20,
                     desc=t("vištiena, špinatai, pomidorai, sūris",
                            "курица, шпинат, помидоры, сыр",
                            "chicken, spinach, tomatoes, cheese")),
                item(3, t("Pachmo", "Пахмо", "Pachmo"), 7.50,
                     desc=t("kiauliena, jautiena, saliamis, alyvuogės, grybai, marinuoti agurkai, du sūriai",
                            "свинина, говядина, салями, оливки, грибы, маринованные огурцы, два сыра",
                            "pork, beef, salami, olives, mushrooms, pickles, two cheeses")),
            ]},
            {"title": t("Priedai", "Добавки", "Add-ons"), "note": "addons_note", "compact": True, "items": [
                item(None, t("Grybai", "Грибы", "Mushrooms"), 0.80),
                item(None, t("Jalapenai", "Халапеньо", "Jalapeños"), 0.80),
                item(None, t("Saliamis", "Салями", "Salami"), 0.80),
                item(None, t("Alyvuogės", "Оливки", "Olives"), 0.80),
                item(None, t("Marinuoti agurkai", "Маринованные огурцы", "Pickles"), 0.80),
                item(None, t("Sūris", "Сыр", "Cheese"), 1.00),
                item(None, t("Daugiau mėsos", "Больше мяса", "Extra meat"), 1.00),
                item(None, t("2-jų rūšių sūris", "Два вида сыра", "Two-cheese blend"), 1.20),
            ]},
        ],
    },
    {
        "id": "koldunai",
        "page": 1,
        "title": t("Koldūnai bei bulvytės", "Колдуны и картофель фри", "Dumplings & fries"),
        "groups": [
            {"title": None, "items": [
                item(1, t("Užkeptos bulvytės „FRI“ su pikantišku padažu ir sūriu",
                          "Запечённый картофель фри с пикантным соусом и сыром",
                          "Baked fries with spicy sauce and cheese"), 6.50),
                item(2, t("Užkeptos bulvytės „FRI“ su pikantišku padažu, kepta šonine ir sūriu",
                          "Запечённый картофель фри с пикантным соусом, жареным беконом и сыром",
                          "Baked fries with spicy sauce, fried bacon and cheese"), 7.50),
                item(3, t("Užkepti gruzdinti koldūnai su padažu ir sūriu",
                          "Запечённые жареные колдуны с соусом и сыром",
                          "Baked fried dumplings with sauce and cheese"), 7.50),
                item(4, t("Užkepti gruzdinti koldūnai su kepta šonine, padažu ir sūriu",
                          "Запечённые жареные колдуны с жареным беконом, соусом и сыром",
                          "Baked fried dumplings with fried bacon, sauce and cheese"), 8.50),
            ]},
            {"title": t("Priedai", "Добавки", "Add-ons"), "note": "koldunai_addons_note", "compact": True, "items": [
                item(None, t("Jalapenai", "Халапеньо", "Jalapeños"), 0.60),
                item(None, t("Kepinti svogūnų dribsniai", "Жареный хрустящий лук", "Crispy fried onions"), 0.60),
            ]},
        ],
    },
    {
        "id": "bulviniai",
        "page": 1,
        "title": t("Bulviniai patiekalai", "Картофельные блюда", "Potato dishes"),
        "two_prices": True,
        "groups": [
            {"title": t("Žemaičių blynai", "Жемайтские блины", "Samogitian pancakes"), "items": [
                item(1, t("Žemaičių blynai su kiauliena", "Жемайтские блины со свининой", "Samogitian pancakes with pork"), 7.40, half=4.00),
                item(2, t("Žemaičių blynai su vištiena", "Жемайтские блины с курицей", "Samogitian pancakes with chicken"), 7.60, half=4.20),
                item(3, t("Žemaičių blynai su pievagrybiais", "Жемайтские блины с шампиньонами", "Samogitian pancakes with mushrooms"), 7.60, half=4.20),
            ]},
            {"title": t("Bulviniai blynai", "Картофельные блины", "Potato pancakes"), "items": [
                item(1, t("Bulvinukai su sūriu", "Картофельные оладьи с сыром", "Potato cakes with cheese"), 7.60, half=4.20),
                item(2, t("Bulviniai blynai su mėsa „Kėdainių“", "Картофельные блины с мясом «Кедайняй»", "Kėdainiai-style potato pancakes with meat"), 7.40, half=4.00),
                item(3, t("Traškūs bulviniai blynai be mėsos", "Хрустящие картофельные блины без мяса", "Crispy potato pancakes, meatless"), 5.00, half=3.20),
            ]},
            {"title": t("Padažai", "Соусы", "Sauces"), "note": "potato_sauce_note", "compact": True, "items": [
                item(None, t("Grietinė", "Сметана", "Sour cream"), 0.00),
                item(None, t("Spirgučiai ir grietinė", "Шкварки и сметана", "Cracklings & sour cream"), 0.40),
                item(None, t("Pievagrybių padažas", "Грибной соус", "Mushroom sauce"), 0.60),
            ]},
        ],
    },
    {
        "id": "uzkandziai",
        "page": 2,
        "title": t("Užkandžiai", "Закуски", "Snacks"),
        "groups": [
            {"title": None, "items": [
                item(1, t("Gruzdintos bulvytės „FRI“ su pomidorų padažu", "Картофель фри с томатным соусом", "French fries with tomato sauce"), 3.50),
                item(2, t("Svogūnų žiedai su česnakiniu padažu", "Луковые кольца с чесночным соусом", "Onion rings with garlic sauce"), 4.50, desc=t("12 vnt.", "12 шт.", "12 pcs")),
                item(3, t("Kalmarų žiedai su česnakiniu padažu", "Кольца кальмара с чесночным соусом", "Squid rings with garlic sauce"), 5.50, desc=t("12 vnt.", "12 шт.", "12 pcs")),
                item(4, t("Vištienos filė piršteliai su pikantišku padažu", "Куриные палочки из филе с пикантным соусом", "Chicken fillet fingers with spicy sauce"), 6.50, desc=t("8 vnt.", "8 шт.", "8 pcs")),
                item(5, t("Kepta duona sūrio padaže", "Жареный хлеб в сырном соусе", "Fried bread in cheese sauce"), 5.50),
                item(6, t("Kepta duona su sūrio padažu", "Жареный хлеб с сырным соусом", "Fried bread with cheese sauce"), 5.50, desc=t("padažas atskirai", "соус отдельно", "sauce on the side")),
                item(7, t("Gruzdinti naminiai koldūnai su mėsa ir pikantišku padažu", "Жареные домашние колдуны с мясом и пикантным соусом", "Fried homemade meat dumplings with spicy sauce"), 6.50),
                item(8, t("Užkandžių rinkinys", "Ассорти закусок", "Snack platter"), 18.50),
            ]},
            {"title": t("Padažai", "Соусы", "Sauces"), "items": [
                item(1, t("Česnakinis padažas", "Чесночный соус", "Garlic sauce"), 1.00),
                item(2, t("Pikantiškas padažas", "Пикантный соус", "Spicy sauce"), 1.00),
                item(3, t("Pomidorų padažas", "Томатный соус", "Tomato sauce"), 1.00),
            ]},
        ],
    },
    {
        "id": "sriubos",
        "page": 2,
        "title": t("Sriubos", "Супы", "Soups"),
        "groups": [
            {"title": None, "items": [
                item(1, t("Natūralus vištienos sultinys", "Натуральный куриный бульон", "Natural chicken broth"), 2.50),
                item(2, t("Dienos sriuba", "Суп дня", "Soup of the day"), 2.60, desc=t("perkant čebureką", "при покупке чебурека", "with a cheburek")),
                item(3, t("Dienos sriuba", "Суп дня", "Soup of the day"), 3.50),
                item(4, t("Šaltibarščiai su bulvėmis", "Холодный свекольник с картофелем", "Cold beetroot soup with potatoes"), 4.50),
            ]},
        ],
    },
    {
        "id": "komplektai",
        "page": 2,
        "title": t("Komplektai", "Комплекты", "Meal deals"),
        "groups": [
            {"title": None, "items": [
                item(1, t("Čeburekas su kiauliena ir sriuba", "Чебурек со свининой и суп", "Pork cheburek & soup"), 6.90),
                item(2, t("Čeburekas su kiauliena + vištienos sultinys", "Чебурек со свининой + куриный бульон", "Pork cheburek + chicken broth"), 6.90),
                item(3, t("Pietūs", "Обед", "Lunch"), 6.90, desc=t("11:00 – 14:00", "11:00 – 14:00", "11:00 – 14:00")),
                item(4, t("Pietūs su sriuba", "Обед с супом", "Lunch with soup"), 6.90, desc=t("11:00 – 14:00", "11:00 – 14:00", "11:00 – 14:00")),
            ]},
        ],
    },
    {
        "id": "kepiniai",
        "page": 2,
        "title": t("Kepiniai", "Выпечка", "Pastries"),
        "groups": [
            {"title": t("Kibinai", "Кибины", "Kibinai"), "items": [
                item(1, t("Kibinas su kiauliena", "Кибин со свининой", "Kibinas with pork"), 2.20),
                item(2, t("Kibinas su kopūstais", "Кибин с капустой", "Kibinas with cabbage"), 2.00),
                item(3, t("Kibinas su varške ir persikais", "Кибин с творогом и персиками", "Kibinas with cottage cheese & peaches"), 2.20),
                item(4, t("Kibinas su vištiena", "Кибин с курицей", "Kibinas with chicken"), 2.40),
                item(5, t("Kibinas su kiauliena ir sūriu", "Кибин со свининой и сыром", "Kibinas with pork & cheese"), 2.50),
            ]},
            {"title": t("Spurgos", "Пончики", "Doughnuts"), "items": [
                item(1, t("Varškės spurga", "Творожный пончик", "Cottage cheese doughnut"), 1.20),
            ]},
        ],
    },
    {
        "id": "desertai",
        "page": 2,
        "title": t("Desertai", "Десерты", "Desserts"),
        "groups": [
            {"title": None, "items": [
                item(1, t("Žagarėliai su ledais ir padažu", "Хворост с мороженым и соусом", "Crispy pastry twists with ice cream and sauce"), 5.50),
                item(2, t("Ledai su vyšnių užpilu", "Мороженое с вишнёвым топпингом", "Ice cream with cherry topping"), 3.90),
                item(3, t("Ledai su šokolado užpilu", "Мороженое с шоколадным топпингом", "Ice cream with chocolate topping"), 3.90),
                item(4, t("Ledai su karamelės užpilu", "Мороженое с карамельным топпингом", "Ice cream with caramel topping"), 3.90),
            ]},
        ],
    },
    {
        "id": "vaikams",
        "page": 2,
        "title": t("Vaikų meniu", "Детское меню", "Kids' menu"),
        "groups": [
            {"title": None, "items": [
                item(1, t("Gruzdintos bulvytės „FRI“ su pomidorų padažu", "Картофель фри с томатным соусом", "French fries with tomato sauce"), 3.50),
                item(2, t("Bulvių ir vištienos rutuliukai", "Картофельно-куриные шарики", "Potato & chicken balls"), 4.90, desc=t("be padažo", "без соуса", "no sauce")),
                item(3, t("Vištienos filė juostelės su bulvytėmis", "Куриные стрипсы с картофелем фри", "Chicken fillet strips with fries"), 5.90, desc=t("su agurkais ir pomidorų padažu", "с огурцами и томатным соусом", "with cucumbers and tomato sauce")),
                item(4, t("Pieniškos dešrelės su bulvytėmis", "Молочные сосиски с картофелем фри", "Milk sausages with fries"), 5.50, desc=t("su agurkais ir pomidorų padažu", "с огурцами и томатным соусом", "with cucumbers and tomato sauce")),
            ]},
        ],
    },
    {
        "id": "gerimai",
        "page": 3,
        "title": t("Gėrimai", "Напитки", "Drinks"),
        "groups": [
            {"title": t("Karšti gėrimai", "Горячие напитки", "Hot drinks"), "items": [
                item(1, t("Arbata iš pakelio", "Чай в пакетике", "Tea (bag)"), 1.20, desc=t("juoda / žalia / vaisinė", "чёрный / зелёный / фруктовый", "black / green / fruit")),
                item(2, t("Plikyta arbata", "Заварной чай", "Brewed loose-leaf tea"), 3.50, desc=t("juoda / žalia / vaisinė", "чёрный / зелёный / фруктовый", "black / green / fruit")),
                item(3, t("Pasiflorų arbata", "Чай с маракуйей", "Passion fruit tea"), 4.50),
                item(4, t("Aviečių arbata", "Малиновый чай", "Raspberry tea"), 4.50),
                item(5, t("Šaltalankių-imbierų arbata", "Облепихово-имбирный чай", "Sea buckthorn & ginger tea"), 4.50),
                item(6, t("Kava juoda", "Чёрный кофе", "Black coffee"), 2.20),
                item(7, t("Kava Espresso", "Эспрессо", "Espresso"), 2.20),
                item(8, t("Kava Latte", "Латте", "Latte"), 2.50),
                item(9, t("Kava Cappuccino", "Капучино", "Cappuccino"), 2.50),
                item(10, t("Kava be kofeino", "Кофе без кофеина", "Decaf coffee"), 2.50),
            ]},
            {"title": t("Šalti gėrimai", "Холодные напитки", "Cold drinks"), "items": [
                item(1, t("Apelsinų sultys", "Апельсиновый сок", "Orange juice"), 2.50),
                item(2, t("Obuolių sultys", "Яблочный сок", "Apple juice"), 2.50),
                item(3, t("Pomidorų sultys", "Томатный сок", "Tomato juice"), 2.50),
                item(4, t("Pepsi", "Pepsi", "Pepsi"), 1.80, vol=330),
                item(5, t("7UP", "7UP", "7UP"), 1.80, vol=330),
                item(6, t("Mirinda", "Mirinda", "Mirinda"), 1.80, vol=330),
                item(7, t("Gira „Vilkmergės“", "Квас «Vilkmergės»", "Vilkmergės kvass"), 2.80, vol=400),
                item(8, t("Pilstoma gira „TAURAS“", "Разливной квас «TAURAS»", "TAURAS draught kvass"), 3.30, vol=500),
                item(9, t("Gazuotas vanduo", "Газированная вода", "Sparkling water"), 1.20, vol=500),
                item(10, t("Negazuotas vanduo", "Негазированная вода", "Still water"), 1.20, vol=500),
                item(11, t("Energetinis gėrimas CULT", "Энергетический напиток CULT", "CULT energy drink"), 2.00, n18=True),
                item(12, t("Nealkoholinis alus „Vilkmergės“", "Безалкогольное пиво «Vilkmergės»", "Vilkmergės non-alcoholic beer"), 3.60, n18=True),
            ]},
        ],
    },
    {
        "id": "alkoholis",
        "page": 3,
        "title": t("Alkoholis", "Алкоголь", "Alcohol"),
        "n18": True,
        "groups": [
            {"title": t("Pilstomas alus", "Разливное пиво", "Draught beer"), "items": [
                item(1, t("„Vilkmergės Premium lager“", "«Vilkmergės Premium lager»", "Vilkmergės Premium lager"), 4.40, vol=400),
                item(2, t("„Vilkmergės“ tamsusis", "«Vilkmergės» тёмное", "Vilkmergės dark"), 4.40, vol=400),
            ]},
            {"title": t("Sidras", "Сидр", "Cider"), "items": [
                item(1, t("Vilkmergės CRAFT sidras", "Сидр Vilkmergės CRAFT", "Vilkmergės CRAFT cider"), 4.60, vol=400),
            ]},
            {"title": t("Vynas", "Вино", "Wine"), "items": [
                item(1, t("Baltasis sausas vynas „COLPASSO“", "Белое сухое вино «COLPASSO»", "COLPASSO dry white wine"), 5.50, vol=150),
                item(2, t("Baltasis sausas vynas „COLPASSO“", "Белое сухое вино «COLPASSO»", "COLPASSO dry white wine"), 22.00, vol=750),
                item(3, t("Raudonasis sausas vynas „PRECISO“", "Красное сухое вино «PRECISO»", "PRECISO dry red wine"), 5.70, vol=150),
                item(4, t("Raudonasis sausas vynas „PRECISO“", "Красное сухое вино «PRECISO»", "PRECISO dry red wine"), 24.00, vol=750),
                item(5, t("Raudonas saldus vynas „KAGOR“", "Красное сладкое вино «KAGOR»", "KAGOR sweet red wine"), 5.20, vol=150),
                item(6, t("Raudonas saldus vynas „KAGOR“", "Красное сладкое вино «KAGOR»", "KAGOR sweet red wine"), 20.00, vol=750),
                item(7, t("Putojantis vynas „Escanza PROSECCO“", "Игристое вино «Escanza PROSECCO»", "Escanza PROSECCO sparkling wine"), 5.50, vol=150),
                item(8, t("Putojantis vynas „Escanza PROSECCO“", "Игристое вино «Escanza PROSECCO»", "Escanza PROSECCO sparkling wine"), 22.00, vol=750),
                item(9, t("Putojantis saldus vynas „BOSCA“", "Игристое сладкое вино «BOSCA»", "BOSCA sweet sparkling wine"), 3.90, vol=150),
                item(10, t("Putojantis saldus vynas „BOSCA“", "Игристое сладкое вино «BOSCA»", "BOSCA sweet sparkling wine"), 13.00, vol=750),
                item(11, t("Karštas vynas", "Глинтвейн", "Mulled wine"), 5.40, vol=150),
            ]},
            {"title": t("Stiprieji gėrimai", "Крепкие напитки", "Spirits"), "items": [
                item(1, t("Degtinė LITHUANIAN AUKSINĖ", "Водка LITHUANIAN AUKSINĖ", "LITHUANIAN AUKSINĖ vodka"), 3.50, vol=40),
                item(2, t("Degtinė LITHUANIAN AUKSINĖ", "Водка LITHUANIAN AUKSINĖ", "LITHUANIAN AUKSINĖ vodka"), 30.00, vol=500),
                item(3, t("Viskis JACK DANIEL’S", "Виски JACK DANIEL’S", "JACK DANIEL’S whiskey"), 4.60, vol=40),
                item(4, t("Viskis JACK DANIEL’S", "Виски JACK DANIEL’S", "JACK DANIEL’S whiskey"), 40.00, vol=500),
            ]},
        ],
    },
]

PAGES = {
    1: t("Čeburekai ir karšti patiekalai", "Чебуреки и горячие блюда", "Chebureki & hot dishes"),
    2: t("Užkandžiai, sriubos ir desertai", "Закуски, супы и десерты", "Snacks, soups & desserts"),
    3: t("Gėrimai ir alkoholis", "Напитки и алкоголь", "Drinks & alcohol"),
}

# Short labels for the mobile section bar.
NAV = {
    "ceburekai": t("Čeburekai", "Чебуреки", "Chebureki"),
    "koldunai": t("Koldūnai", "Колдуны", "Dumplings"),
    "bulviniai": t("Bulviniai", "Картофельные", "Potato"),
    "uzkandziai": t("Užkandžiai", "Закуски", "Snacks"),
    "sriubos": t("Sriubos", "Супы", "Soups"),
    "komplektai": t("Komplektai", "Комплекты", "Deals"),
    "kepiniai": t("Kepiniai", "Выпечка", "Pastries"),
    "desertai": t("Desertai", "Десерты", "Desserts"),
    "vaikams": t("Vaikams", "Детям", "Kids"),
    "gerimai": t("Gėrimai", "Напитки", "Drinks"),
    "alkoholis": t("Alkoholis", "Алкоголь", "Alcohol"),
}
