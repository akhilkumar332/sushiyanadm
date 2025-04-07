// /skripte/translate.js
document.addEventListener('DOMContentLoaded', function() {
    const BASE_PATH = document.body.dataset.basePath || '/';
    const languageDropdown = document.getElementById('language-dropdown');
    const branchDropdown = document.getElementById('branch-dropdown');
    let isLanguageChanging = false;

    // Translation dictionary for static text and toast messages
    const translations = {
        'de': {
            'impressum': 'Impressum',
            'datenschutz': 'Datenschutz',
            'select_branch': 'Filiale wählen:',
            'select_language': 'Sprache wählen:',
            'footer_copyright': 'Sushi Yana © Alle Rechte vorbehalten. 2025',
            'no_items_found': 'Keine Artikel gefunden.',
            'allergens_additives': 'Allergene und Zusatzstoffe',
            'ingredients': 'Zutaten',
            'page_title': 'Digitale Speisekarte',
            'sushi_page_title': 'Digitale Speisekarte - Sushi',
            'vegetarian_page_title': 'Digitale Speisekarte - Vegetarisch',
            'yana_menu_page_title': 'Digitale Speisekarte - Yana Menü',
            'warmekueche_menu_page_title': 'Digitale Speisekarte - Warme Küche Menü',
            'cart_page_title': 'Digitale Speisekarte - Warenkorb',
            'invoice_page_title': 'Digitale Speisekarte - Rechnung',
            'soup': 'Suppe',
            'starter': 'Vorspeise',
            'gyoza': 'Gyoza',
            'salad': 'Salat',
            'summer_roll': 'Sommerrolle',
            'sushi': 'Sushi',
            'warm_kitchen': 'Warme Küche',
            'bowl': 'Bowl',
            'dessert': 'Dessert',
            'extras': 'Extras',
            'drinks': 'Getränke',
            'warm_drinks': 'Warme Getränke',
            'menus': 'Menüs',
            'sashimi': 'Sashimi',
            'makis': 'Makis',
            'inside_out_rolls': 'Inside Out Rolls',
            'mini_yana_rolls': 'Mini Yana Rolls',
            'yana_rolls': 'Yana Rolls',
            'nigiris': 'Nigiris',
            'special_rolls': 'Special Rolls',
            'temaki': 'Temaki',
            'vegetarian': 'Vegetarisch',
            'chop_suey': 'Chop Suey',
            'peanut_dish': 'Erdnussgericht',
            'vegetables': 'Gemüse',
            'mango_chutney': 'Mango Chutney',
            'noodles': 'Nudeln',
            'red_curry': 'Rotes Curry',
            'rice': 'Reis',
            'sweet_sour_sauce': 'Süß Sauer Sauce',
            'yellow_curry': 'Gelbes Curry',
            'your_cart': 'Ihr Warenkorb',
            'cart_empty': 'Ihr Warenkorb ist leer.',
            'total_amount': 'Gesamtbetrag',
            'confirm': 'Bestätigen',
            'your_invoice': 'Ihre Rechnung - Bestellbestätigung',
            'number': 'Nummer',
            'item': 'Artikel',
            'quantity': 'Menge',
            'unit_price': 'Einzelpreis',
            'subtotal': 'Gesamt',
            'inactivity_timer': 'Inaktivitätstimer: <span id="timer-countdown"></span>',
            'seconds': 'Sekunden',
            'delete_return': 'Löschen & Zurückgeben',
            'notify_staff': 'Personal benachrichtigen',
            'order_placed': 'Bestellung aufgegeben! Bitte warten Sie, bis ein Mitarbeiter zu Ihnen kommt.',
            'select_your_table': 'Wählen Sie Ihren Tisch aus:',
            'select_table': 'Tisch auswählen',
            'table_n': 'Tisch %d',
            'language_changed': 'Sprache geändert zu ',
            'item_removed': 'Artikel entfernt',
            'quantity_increased': 'Menge erhöht',
            'quantity_decreased': 'Menge reduziert',
            'update_error': 'Fehler beim Aktualisieren: ',
            'server_error': 'Serverfehler',
            'menu_update_error': 'Fehler beim Aktualisieren der Menüliste',
            'branch_load_error': 'Fehler beim Laden der Filialen: ',
            'branch_load_server_error': 'Fehler beim Laden der Filialen: Serverfehler',
            'branch_changed': 'Filiale geändert zu ',
            'branch_change_error': 'Fehler beim Ändern der Filiale: ',
            'cart_restore_error': 'Fehler beim Wiederherstellen des Warenkorbs',
            'remove_error': 'Fehler beim Entfernen: ',
            'table_select_error': 'Bitte wählen Sie einen Tisch aus.',
            'order_submit_success': 'Bestellung erfolgreich übermittelt',
            'order_submit_error': 'Fehler beim Übermitteln: ',
            'active_orders_error': 'Fehler beim Laden aktiver Bestellungen: ',
            'completed_orders_error': 'Fehler beim Laden abgeschlossener Bestellungen: ',
            'order_completed': 'Bestellung abgeschlossen',
            'order_deleted': 'Bestellung gelöscht',
            'order_complete_error': 'Fehler beim Abschließen: ',
            'order_delete_error': 'Fehler beim Löschen: ',
            'new_order_received': 'Neue Bestellung #',
            'title': 'Impressum',
            'responsible_text': 'Verantwortlich für die Startseite und die weiteren Informationsseiten (ohne Onlineshops) dieser Webseite ist:',
            'manager_label': 'Geschäftsführer: %s',
            'tax_label': 'Steuernummer: %s',
            'court_label': 'Zuständiges Gericht: %s',
            'register_label': 'Handelsregister: %s',
            'vat_label': 'Ust.: %s',
            'franchise_note': 'Jede Sushi Yana Filiale wird von einem selbstständig tätigen Gewerbetreibenden als Franchisenehmer bewirtschaftet. Dieser organisiert Produktion und Auslieferung seiner Produkte für seinen Betrieb in eigener Verantwortung. Wenn du Fragen oder Anliegen zu deiner Lieferung hast, wende dich bitte an den Verantwortlichen des jeweiligen Betriebes, den du in vorstehender Liste finden kannst.',
            'contact_note': 'Unsere Franchisezentrale erreichen Sie über die Mailadresse',
            'contact_email': 'buero@sushi-yana.de',
            'contact_instruction': 'Bitte nutzen Sie diese ausschließlich für allgemeine Anfragen.'
        },
        'en': {
            'impressum': 'Imprint',
            'datenschutz': 'Privacy Policy',
            'select_branch': 'Select Branch:',
            'select_language': 'Select Language:',
            'footer_copyright': 'Sushi Yana © All Rights Reserved. 2025',
            'no_items_found': 'No items found.',
            'allergens_additives': 'Allergens and Additives',
            'ingredients': 'Ingredients',
            'page_title': 'Digital Menu',
            'sushi_page_title': 'Digital Menu - Sushi',
            'vegetarian_page_title': 'Digital Menu - Vegetarian',
            'yana_menu_page_title': 'Digital Menu - Yana Menu',
            'warmekueche_menu_page_title': 'Digital Menu - Warm Kitchen Menu',
            'cart_page_title': 'Digital Menu - Cart',
            'invoice_page_title': 'Digital Menu - Invoice',
            'soup': 'Soup',
            'starter': 'Starter',
            'gyoza': 'Gyoza',
            'salad': 'Salad',
            'summer_roll': 'Summer Roll',
            'sushi': 'Sushi',
            'warm_kitchen': 'Warm Kitchen',
            'bowl': 'Bowl',
            'dessert': 'Dessert',
            'extras': 'Extras',
            'drinks': 'Drinks',
            'warm_drinks': 'Warm Drinks',
            'menus': 'Menus',
            'sashimi': 'Sashimi',
            'makis': 'Makis',
            'inside_out_rolls': 'Inside Out Rolls',
            'mini_yana_rolls': 'Mini Yana Rolls',
            'yana_rolls': 'Yana Rolls',
            'nigiris': 'Nigiris',
            'special_rolls': 'Special Rolls',
            'temaki': 'Temaki',
            'vegetarian': 'Vegetarian',
            'chop_suey': 'Chop Suey',
            'peanut_dish': 'Peanut Dish',
            'vegetables': 'Vegetables',
            'mango_chutney': 'Mango Chutney',
            'noodles': 'Noodles',
            'red_curry': 'Red Curry',
            'rice': 'Rice',
            'sweet_sour_sauce': 'Sweet and Sour Sauce',
            'yellow_curry': 'Yellow Curry',
            'your_cart': 'Your Cart',
            'cart_empty': 'Your cart is empty.',
            'total_amount': 'Total Amount',
            'confirm': 'Confirm',
            'your_invoice': 'Your Invoice - Order Confirmation',
            'number': 'Number',
            'item': 'Item',
            'quantity': 'Quantity',
            'unit_price': 'Unit Price',
            'subtotal': 'Subtotal',
            'inactivity_timer': 'Inactivity Timer: <span id="timer-countdown"></span>',
            'seconds': 'Seconds',
            'delete_return': 'Delete & Return',
            'notify_staff': 'Notify Staff',
            'order_placed': 'Order placed! Please wait for a staff member to assist you.',
            'select_your_table': 'Select your table:',
            'select_table': 'Select Table',
            'table_n': 'Table %d',
            'language_changed': 'Language changed to ',
            'item_removed': 'Item removed',
            'quantity_increased': 'Quantity increased',
            'quantity_decreased': 'Quantity decreased',
            'update_error': 'Error updating: ',
            'server_error': 'Server error',
            'menu_update_error': 'Error updating menu list',
            'branch_load_error': 'Error loading branches: ',
            'branch_load_server_error': 'Error loading branches: Server error',
            'branch_changed': 'Branch changed to ',
            'branch_change_error': 'Error changing branch: ',
            'cart_restore_error': 'Error restoring cart',
            'remove_error': 'Error removing: ',
            'table_select_error': 'Please select a table.',
            'order_submit_success': 'Order successfully submitted',
            'order_submit_error': 'Error submitting: ',
            'active_orders_error': 'Error loading active orders: ',
            'completed_orders_error': 'Error loading completed orders: ',
            'order_completed': 'Order completed',
            'order_deleted': 'Order deleted',
            'order_complete_error': 'Error completing: ',
            'order_delete_error': 'Error deleting: ',
            'new_order_received': 'New order #',
            'title': 'Imprint',
            'responsible_text': 'Responsible for the homepage and the additional information pages (excluding online shops) of this website is:',
            'manager_label': 'Manager: %s',
            'tax_label': 'Tax Number: %s',
            'court_label': 'Court: %s',
            'register_label': 'Register: %s',
            'vat_label': 'VAT: %s',
            'franchise_note': 'Each Sushi Yana branch is operated by an independent franchisee. They are responsible for the production and delivery of their products for their own business. If you have any questions or concerns regarding your delivery, please contact the responsible person of the respective branch, whom you can find in the list above.',
            'contact_note': 'You can reach our franchise headquarters via the email address',
            'contact_email': 'buero@sushi-yana.de',
            'contact_instruction': 'Please use this exclusively for general inquiries.'
        },
        'fr': {
            'impressum': 'Mentions légales',
            'datenschutz': 'Politique de confidentialité',
            'select_branch': 'Sélectionner une succursale :',
            'select_language': 'Choisir la langue :',
            'footer_copyright': 'Sushi Yana © Tous droits réservés. 2025',
            'no_items_found': 'Aucun article trouvé.',
            'allergens_additives': 'Allergènes et additifs',
            'ingredients': 'Ingrédients',
            'page_title': 'Menu numérique',
            'sushi_page_title': 'Menu numérique - Sushi',
            'vegetarian_page_title': 'Menu numérique - Végétarien',
            'yana_menu_page_title': 'Menu numérique - Menu Yana',
            'warmekueche_menu_page_title': 'Menu numérique - Menu Cuisine Chaude',
            'cart_page_title': 'Menu numérique - Panier',
            'invoice_page_title': 'Menu numérique - Facture',
            'soup': 'Soupe',
            'starter': 'Entrée',
            'gyoza': 'Gyoza',
            'salad': 'Salade',
            'summer_roll': 'Rouleau d’été',
            'sushi': 'Sushi',
            'warm_kitchen': 'Cuisine chaude',
            'bowl': 'Bol',
            'dessert': 'Dessert',
            'extras': 'Extras',
            'drinks': 'Boissons',
            'warm_drinks': 'Boissons chaudes',
            'menus': 'Menus',
            'sashimi': 'Sashimi',
            'makis': 'Makis',
            'inside_out_rolls': 'Rouleaux inversés',
            'mini_yana_rolls': 'Mini rouleaux Yana',
            'yana_rolls': 'Rouleaux Yana',
            'nigiris': 'Nigiris',
            'special_rolls': 'Rouleaux spéciaux',
            'temaki': 'Temaki',
            'vegetarian': 'Végétarien',
            'chop_suey': 'Chop Suey',
            'peanut_dish': 'Plat aux arachides',
            'vegetables': 'Légumes',
            'mango_chutney': 'Chutney de mangue',
            'noodles': 'Nouilles',
            'red_curry': 'Curry rouge',
            'rice': 'Riz',
            'sweet_sour_sauce': 'Sauce aigre-douce',
            'yellow_curry': 'Curry jaune',
            'your_cart': 'Votre panier',
            'cart_empty': 'Votre panier est vide.',
            'total_amount': 'Montant total',
            'confirm': 'Confirmer',
            'your_invoice': 'Votre facture - Confirmation de commande',
            'number': 'Numéro',
            'item': 'Article',
            'quantity': 'Quantité',
            'unit_price': 'Prix unitaire',
            'subtotal': 'Sous-total',
            'inactivity_timer': 'Minuteur d’inactivité : <span id="timer-countdown"></span>',
            'seconds': 'Secondes',
            'delete_return': 'Supprimer et retourner',
            'notify_staff': 'Notifier le personnel',
            'order_placed': 'Commande passée ! Veuillez attendre qu’un membre du personnel vienne vous aider.',
            'select_your_table': 'Sélectionnez votre table :',
            'select_table': 'Sélectionner une table',
            'table_n': 'Table %d',
            'language_changed': 'Langue changée en ',
            'item_removed': 'Article supprimé',
            'quantity_increased': 'Quantité augmentée',
            'quantity_decreased': 'Quantité réduite',
            'update_error': 'Erreur de mise à jour : ',
            'server_error': 'Erreur serveur',
            'menu_update_error': 'Erreur de mise à jour de la liste des menus',
            'branch_load_error': 'Erreur de chargement des succursales : ',
            'branch_load_server_error': 'Erreur de chargement des succursales : Erreur serveur',
            'branch_changed': 'Succursale changée en ',
            'branch_change_error': 'Erreur de changement de succursale : ',
            'cart_restore_error': 'Erreur de restauration du panier',
            'remove_error': 'Erreur de suppression : ',
            'table_select_error': 'Veuillez sélectionner une table.',
            'order_submit_success': 'Commande soumise avec succès',
            'order_submit_error': 'Erreur de soumission : ',
            'active_orders_error': 'Erreur de chargement des commandes actives : ',
            'completed_orders_error': 'Erreur de chargement des commandes terminées : ',
            'order_completed': 'Commande terminée',
            'order_deleted': 'Commande supprimée',
            'order_complete_error': 'Erreur de finalisation : ',
            'order_delete_error': 'Erreur de suppression : ',
            'new_order_received': 'Nouvelle commande #',
            'title': 'Mentions légales',
            'responsible_text': 'Responsable de la page d’accueil et des pages d’information supplémentaires (hors boutiques en ligne) de ce site web est :',
            'manager_label': 'Manager : %s',
            'tax_label': 'Numéro fiscal : %s',
            'court_label': 'Tribunal compétent : %s',
            'register_label': 'Registre du commerce : %s',
            'vat_label': 'TVA : %s',
            'franchise_note': 'Chaque succursale Sushi Yana est gérée par un franchisé indépendant. Il est responsable de la production et de la livraison de ses produits pour son propre établissement. Si vous avez des questions ou des préoccupations concernant votre livraison, veuillez contacter le responsable de la succursale concernée, que vous pouvez trouver dans la liste ci-dessus.',
            'contact_note': 'Vous pouvez joindre notre siège de franchise via l’adresse email',
            'contact_email': 'buero@sushi-yana.de',
            'contact_instruction': 'Veuillez utiliser cette adresse uniquement pour des demandes générales.'
        },
        'pl': {
            'impressum': 'Impressum',
            'datenschutz': 'Polityka prywatności',
            'select_branch': 'Wybierz oddział:',
            'select_language': 'Wybierz język:',
            'footer_copyright': 'Sushi Yana © Wszystkie prawa zastrzeżone. 2025',
            'no_items_found': 'Nie znaleziono żadnych pozycji.',
            'allergens_additives': 'Alergeny i dodatki',
            'ingredients': 'Składniki',
            'page_title': 'Cyfrowe menu',
            'sushi_page_title': 'Cyfrowe menu - Sushi',
            'vegetarian_page_title': 'Cyfrowe menu - Wegetariańskie',
            'yana_menu_page_title': 'Cyfrowe menu - Menu Yana',
            'warmekueche_menu_page_title': 'Cyfrowe menu - Menu Ciepłej Kuchni',
            'cart_page_title': 'Cyfrowe menu - Koszyk',
            'invoice_page_title': 'Cyfrowe menu - Faktura',
            'soup': 'Zupa',
            'starter': 'Przystawka',
            'gyoza': 'Gyoza',
            'salad': 'Sałatka',
            'summer_roll': 'Rolka letnia',
            'sushi': 'Sushi',
            'warm_kitchen': 'Ciepła kuchnia',
            'bowl': 'Miska',
            'dessert': 'Deser',
            'extras': 'Dodatki',
            'drinks': 'Napoje',
            'warm_drinks': 'Ciepłe napoje',
            'menus': 'Menu',
            'sashimi': 'Sashimi',
            'makis': 'Maki',
            'inside_out_rolls': 'Rolki na lewą stronę',
            'mini_yana_rolls': 'Mini rolki Yana',
            'yana_rolls': 'Rolki Yana',
            'nigiris': 'Nigiri',
            'special_rolls': 'Specjalne rolki',
            'temaki': 'Temaki',
            'vegetarian': 'Wegetariańskie',
            'chop_suey': 'Chop Suey',
            'peanut_dish': 'Danie z orzeszkami',
            'vegetables': 'Warzywa',
            'mango_chutney': 'Chutney z mango',
            'noodles': 'Makaron',
            'red_curry': 'Czerwone curry',
            'rice': 'Ryż',
            'sweet_sour_sauce': 'Sos słodko-kwaśny',
            'yellow_curry': 'Żółte curry',
            'your_cart': 'Twój koszyk',
            'cart_empty': 'Twój koszyk jest pusty.',
            'total_amount': 'Łączna kwota',
            'confirm': 'Potwierdź',
            'your_invoice': 'Twoja faktura - Potwierdzenie zamówienia',
            'number': 'Numer',
            'item': 'Pozycja',
            'quantity': 'Ilość',
            'unit_price': 'Cena jednostkowa',
            'subtotal': 'Podsuma',
            'inactivity_timer': 'Timer bezczynności: <span id="timer-countdown"></span>',
            'seconds': 'Sekundy',
            'delete_return': 'Usuń i wróć',
            'notify_staff': 'Powiadom personel',
            'order_placed': 'Zamówienie złożone! Proszę czekać na pomoc pracownika.',
            'select_your_table': 'Wybierz swój stolik:',
            'select_table': 'Wybierz stolik',
            'table_n': 'Stolik %d',
            'language_changed': 'Język zmieniony na ',
            'item_removed': 'Pozycja usunięta',
            'quantity_increased': 'Ilość zwiększona',
            'quantity_decreased': 'Ilość zmniejszona',
            'update_error': 'Błąd aktualizacji: ',
            'server_error': 'Błąd serwera',
            'menu_update_error': 'Błąd aktualizacji listy menu',
            'branch_load_error': 'Błąd ładowania oddziałów: ',
            'branch_load_server_error': 'Błąd ładowania oddziałów: Błąd serwera',
            'branch_changed': 'Oddział zmieniony na ',
            'branch_change_error': 'Błąd zmiany oddziału: ',
            'cart_restore_error': 'Błąd przywracania koszyka',
            'remove_error': 'Błąd usuwania: ',
            'table_select_error': 'Proszę wybrać stolik.',
            'order_submit_success': 'Zamówienie pomyślnie złożone',
            'order_submit_error': 'Błąd składania zamówienia: ',
            'active_orders_error': 'Błąd ładowania aktywnych zamówień: ',
            'completed_orders_error': 'Błąd ładowania zakończonych zamówień: ',
            'order_completed': 'Zamówienie zakończone',
            'order_deleted': 'Zamówienie usunięte',
            'order_complete_error': 'Błąd zakończenia: ',
            'order_delete_error': 'Błąd usuwania: ',
            'new_order_received': 'Nowe zamówienie #',
            'title': 'Impressum',
            'responsible_text': 'Odpowiedzialnym za stronę główną oraz dodatkowe strony informacyjne (z wyłączeniem sklepów internetowych) tej witryny jest:',
            'manager_label': 'Manager: %s',
            'tax_label': 'Numer podatkowy: %s',
            'court_label': 'Sąd właściwy: %s',
            'register_label': 'Rejestr handlowy: %s',
            'vat_label': 'VAT: %s',
            'franchise_note': 'Każdy oddział Sushi Yana jest prowadzony przez niezależnego franczyzobiorcę. Odpowiada on za produkcję i dostawę swoich produktów dla swojego przedsiębiorstwa. Jeśli masz pytania lub wątpliwości dotyczące dostawy, skontaktuj się z odpowiedzialną osobą danego oddziału, którą znajdziesz na powyższej liście.',
            'contact_note': 'Naszą centralę franczyzową można skontaktować pod adresem email',
            'contact_email': 'buero@sushi-yana.de',
            'contact_instruction': 'Prosimy używać tego wyłącznie do ogólnych zapytań.'
        },
        'it': {
            'impressum': 'Impressum',
            'datenschutz': 'Informativa sulla privacy',
            'select_branch': 'Seleziona filiale:',
            'select_language': 'Seleziona lingua:',
            'footer_copyright': 'Sushi Yana © Tutti i diritti riservati. 2025',
            'no_items_found': 'Nessun articolo trovato.',
            'allergens_additives': 'Allergeni e additivi',
            'ingredients': 'Ingredienti',
            'page_title': 'Menu digitale',
            'sushi_page_title': 'Menu digitale - Sushi',
            'vegetarian_page_title': 'Menu digitale - Vegetariano',
            'yana_menu_page_title': 'Menu digitale - Menu Yana',
            'warmekueche_menu_page_title': 'Menu digitale - Menu Cucina Calda',
            'cart_page_title': 'Menu digitale - Carrello',
            'invoice_page_title': 'Menu digitale - Fattura',
            'soup': 'Zuppa',
            'starter': 'Antipasto',
            'gyoza': 'Gyoza',
            'salad': 'Insalata',
            'summer_roll': 'Involtino estivo',
            'sushi': 'Sushi',
            'warm_kitchen': 'Cucina calda',
            'bowl': 'Ciotola',
            'dessert': 'Dessert',
            'extras': 'Extra',
            'drinks': 'Bevande',
            'warm_drinks': 'Bevande calde',
            'menus': 'Menu',
            'sashimi': 'Sashimi',
            'makis': 'Maki',
            'inside_out_rolls': 'Rotoli Inside Out',
            'mini_yana_rolls': 'Mini rotoli Yana',
            'yana_rolls': 'Rotoli Yana',
            'nigiris': 'Nigiri',
            'special_rolls': 'Rotoli speciali',
            'temaki': 'Temaki',
            'vegetarian': 'Vegetariano',
            'chop_suey': 'Chop Suey',
            'peanut_dish': 'Piatto alle arachidi',
            'vegetables': 'Verdure',
            'mango_chutney': 'Chutney di mango',
            'noodles': 'Noodles',
            'red_curry': 'Curry rosso',
            'rice': 'Riso',
            'sweet_sour_sauce': 'Salsa agrodolce',
            'yellow_curry': 'Curry giallo',
            'your_cart': 'Il tuo carrello',
            'cart_empty': 'Il tuo carrello è vuoto.',
            'total_amount': 'Importo totale',
            'confirm': 'Conferma',
            'your_invoice': 'La tua fattura - Conferma dell’ordine',
            'number': 'Numero',
            'item': 'Articolo',
            'quantity': 'Quantità',
            'unit_price': 'Prezzo unitario',
            'subtotal': 'Subtotale',
            'inactivity_timer': 'Timer di inattività: <span id="timer-countdown"></span>',
            'seconds': 'Secondi',
            'delete_return': 'Elimina e torna indietro',
            'notify_staff': 'Notifica il personale',
            'order_placed': 'Ordine effettuato! Attendi che un membro dello staff ti assista.',
            'select_your_table': 'Seleziona il tuo tavolo:',
            'select_table': 'Seleziona tavolo',
            'table_n': 'Tavolo %d',
            'language_changed': 'Lingua cambiata in ',
            'item_removed': 'Articolo rimosso',
            'quantity_increased': 'Quantità aumentata',
            'quantity_decreased': 'Quantità ridotta',
            'update_error': 'Errore durante l’aggiornamento: ',
            'server_error': 'Errore del server',
            'menu_update_error': 'Errore durante l’aggiornamento della lista dei menu',
            'branch_load_error': 'Errore nel caricamento delle filiali: ',
            'branch_load_server_error': 'Errore nel caricamento delle filiali: Errore del server',
            'branch_changed': 'Filiale cambiata in ',
            'branch_change_error': 'Errore nel cambio della filiale: ',
            'cart_restore_error': 'Errore nel ripristino del carrello',
            'remove_error': 'Errore nella rimozione: ',
            'table_select_error': 'Seleziona un tavolo.',
            'order_submit_success': 'Ordine inviato con successo',
            'order_submit_error': 'Errore nell’invio: ',
            'active_orders_error': 'Errore nel caricamento degli ordini attivi: ',
            'completed_orders_error': 'Errore nel caricamento degli ordini completati: ',
            'order_completed': 'Ordine completato',
            'order_deleted': 'Ordine eliminato',
            'order_complete_error': 'Errore nel completamento: ',
            'order_delete_error': 'Errore nell’eliminazione: ',
            'new_order_received': 'Nuovo ordine #',
            'title': 'Impressum',
            'responsible_text': 'Responsabile della homepage e delle ulteriori pagine informative (esclusi i negozi online) di questo sito web è:',
            'manager_label': 'Manager: %s',
            'tax_label': 'Numero fiscale: %s',
            'court_label': 'Tribunale competente: %s',
            'register_label': 'Registro delle imprese: %s',
            'vat_label': 'IVA: %s',
            'franchise_note': 'Ogni filiale Sushi Yana è gestita da un franchising indipendente. Questi è responsabile della produzione e della consegna dei propri prodotti per la propria attività. Se hai domande o problemi relativi alla tua consegna, contatta il responsabile della filiale corrispondente, che puoi trovare nell’elenco sopra.',
            'contact_note': 'Puoi raggiungere la nostra sede centrale del franchising tramite l’indirizzo email',
            'contact_email': 'buero@sushi-yana.de',
            'contact_instruction': 'Si prega di utilizzare questo esclusivamente per richieste generali.'
        },
        'ru': {
            'impressum': 'Импрессум',
            'datenschutz': 'Политика конфиденциальности',
            'select_branch': 'Выбрать филиал:',
            'select_language': 'Выбрать язык:',
            'footer_copyright': 'Sushi Yana © Все права защищены. 2025',
            'no_items_found': 'Товары не найдены.',
            'allergens_additives': 'Аллергены и добавки',
            'ingredients': 'Ингредиенты',
            'page_title': 'Цифровое меню',
            'sushi_page_title': 'Цифровое меню - Суши',
            'vegetarian_page_title': 'Цифровое меню - Вегетарианское',
            'yana_menu_page_title': 'Цифровое меню - Меню Яна',
            'warmekueche_menu_page_title': 'Цифровое меню - Меню Тёплой Кухни',
            'cart_page_title': 'Цифровое меню - Корзина',
            'invoice_page_title': 'Цифровое меню - Счёт',
            'soup': 'Суп',
            'starter': 'Закуска',
            'gyoza': 'Гёдза',
            'salad': 'Салат',
            'summer_roll': 'Летний ролл',
            'sushi': 'Суши',
            'warm_kitchen': 'Тёплая кухня',
            'bowl': 'Миска',
            'dessert': 'Десерт',
            'extras': 'Дополнения',
            'drinks': 'Напитки',
            'warm_drinks': 'Тёплые напитки',
            'menus': 'Меню',
            'sashimi': 'Сасими',
            'makis': 'Маки',
            'inside_out_rolls': 'Роллы наизнанку',
            'mini_yana_rolls': 'Мини Яна роллы',
            'yana_rolls': 'Яна роллы',
            'nigiris': 'Нигири',
            'special_rolls': 'Специальные роллы',
            'temaki': 'Темак',
            'vegetarian': 'Вегетарианское',
            'chop_suey': 'Чоп Суэй',
            'peanut_dish': 'Блюдо с арахисом',
            'vegetables': 'Овощи',
            'mango_chutney': 'Манговый чатни',
            'noodles': 'Лапша',
            'red_curry': 'Красный карри',
            'rice': 'Рис',
            'sweet_sour_sauce': 'Кисло-сладкий соус',
            'yellow_curry': 'Жёлтый карри',
            'your_cart': 'Ваша корзина',
            'cart_empty': 'Ваша корзина пуста.',
            'total_amount': 'Общая сумма',
            'confirm': 'Подтвердить',
            'your_invoice': 'Ваш счёт - Подтверждение заказа',
            'number': 'Номер',
            'item': 'Товар',
            'quantity': 'Количество',
            'unit_price': 'Цена за единицу',
            'subtotal': 'Подытог',
            'inactivity_timer': 'Таймер бездействия: <span id="timer-countdown"></span>',
            'seconds': 'Секунд',
            'delete_return': 'Удалить и вернуться',
            'notify_staff': 'Уведомить персонал',
            'order_placed': 'Заказ размещён! Пожалуйста, дождитесь сотрудника.',
            'select_your_table': 'Выберите свой стол:',
            'select_table': 'Выбрать стол',
            'table_n': 'Стол %d',
            'language_changed': 'Язык изменён на ',
            'item_removed': 'Товар удалён',
            'quantity_increased': 'Количество увеличено',
            'quantity_decreased': 'Количество уменьшено',
            'update_error': 'Ошибка обновления: ',
            'server_error': 'Ошибка сервера',
            'menu_update_error': 'Ошибка обновления списка меню',
            'branch_load_error': 'Ошибка загрузки филиалов: ',
            'branch_load_server_error': 'Ошибка загрузки филиалов: Ошибка сервера',
            'branch_changed': 'Филиал изменён на ',
            'branch_change_error': 'Ошибка изменения филиала: ',
            'cart_restore_error': 'Ошибка восстановления корзины',
            'remove_error': 'Ошибка удаления: ',
            'table_select_error': 'Пожалуйста, выберите стол.',
            'order_submit_success': 'Заказ успешно отправлен',
            'order_submit_error': 'Ошибка отправки: ',
            'active_orders_error': 'Ошибка загрузки активных заказов: ',
            'completed_orders_error': 'Ошибка загрузки завершённых заказов: ',
            'order_completed': 'Заказ завершён',
            'order_deleted': 'Заказ удалён',
            'order_complete_error': 'Ошибка завершения: ',
            'order_delete_error': 'Ошибка удаления: ',
            'new_order_received': 'Новый заказ #',
            'title': 'Импрессум',
            'responsible_text': 'Ответственным за главную страницу и дополнительные информационные страницы (кроме интернет-магазинов) этого сайта является:',
            'manager_label': 'Менеджер: %s',
            'tax_label': 'Налоговый номер: %s',
            'court_label': 'Компетентный суд: %s',
            'register_label': 'Торговый реестр: %s',
            'vat_label': 'НДС: %s',
            'franchise_note': 'Каждый филиал Sushi Yana управляется независимым франчайзи. Он отвечает за производство и доставку своих продуктов для своего бизнеса. Если у вас есть вопросы или проблемы с доставкой, пожалуйста, свяжитесь с ответственным лицом соответствующего филиала, которого вы можете найти в списке выше.',
            'contact_note': 'Наш центральный офис франшизы можно связаться по адресу электронной почты',
            'contact_email': 'buero@sushi-yana.de',
            'contact_instruction': 'Пожалуйста, используйте это только для общих запросов.'
        },
        'tr': {
            'impressum': 'Künye',
            'datenschutz': 'Gizlilik Politikası',
            'select_branch': 'Şube Seç:',
            'select_language': 'Dil Seç:',
            'footer_copyright': 'Sushi Yana © Tüm hakları saklıdır. 2025',
            'no_items_found': 'Hiçbir öğe bulunamadı.',
            'allergens_additives': 'Alerjenler ve Katkı Maddeleri',
            'ingredients': 'Malzemeler',
            'page_title': 'Dijital Menü',
            'sushi_page_title': 'Dijital Menü - Suşi',
            'vegetarian_page_title': 'Dijital Menü - Vejetaryen',
            'yana_menu_page_title': 'Dijital Menü - Yana Menü',
            'warmekueche_menu_page_title': 'Dijital Menü - Sıcak Mutfak Menü',
            'cart_page_title': 'Dijital Menü - Sepet',
            'invoice_page_title': 'Dijital Menü - Fatura',
            'soup': 'Çorba',
            'starter': 'Başlangıç',
            'gyoza': 'Gyoza',
            'salad': 'Salata',
            'summer_roll': 'Yaz Rulosu',
            'sushi': 'Suşi',
            'warm_kitchen': 'Sıcak Mutfak',
            'bowl': 'Kase',
            'dessert': 'Tatlı',
            'extras': 'Ekstralar',
            'drinks': 'İçecekler',
            'warm_drinks': 'Sıcak İçecekler',
            'menus': 'Menüler',
            'sashimi': 'Sashimi',
            'makis': 'Maki',
            'inside_out_rolls': 'Ters Yüz Rulolar',
            'mini_yana_rolls': 'Mini Yana Rulolar',
            'yana_rolls': 'Yana Rulolar',
            'nigiris': 'Nigiri',
            'special_rolls': 'Özel Rulolar',
            'temaki': 'Temaki',
            'vegetarian': 'Vejetaryen',
            'chop_suey': 'Chop Suey',
            'peanut_dish': 'Fıstık Yemeği',
            'vegetables': 'Sebzeler',
            'mango_chutney': 'Mango Çatnisi',
            'noodles': 'Erişte',
            'red_curry': 'Kırmızı Köri',
            'rice': 'Pirinç',
            'sweet_sour_sauce': 'Tatlı Ekşi Sos',
            'yellow_curry': 'Sarı Köri',
            'your_cart': 'Sepetiniz',
            'cart_empty': 'Sepetiniz boş.',
            'total_amount': 'Toplam Tutar',
            'confirm': 'Onayla',
            'your_invoice': 'Faturanız - Sipariş Onayı',
            'number': 'Numara',
            'item': 'Öğe',
            'quantity': 'Miktar',
            'unit_price': 'Birim Fiyat',
            'subtotal': 'Ara Toplam',
            'inactivity_timer': 'Hareketsizlik Zamanlayıcısı: <span id="timer-countdown"></span>',
            'seconds': 'Saniye',
            'delete_return': 'Sil ve Geri Dön',
            'notify_staff': 'Personeli Bilgilendir',
            'order_placed': 'Sipariş verildi! Lütfen bir personelin size yardımcı olmasını bekleyin.',
            'select_your_table': 'Masanızı seçin:',
            'select_table': 'Masa Seç',
            'table_n': 'Masa %d',
            'language_changed': 'Dil değiştirildi: ',
            'item_removed': 'Öğe kaldırıldı',
            'quantity_increased': 'Miktar artırıldı',
            'quantity_decreased': 'Miktar azaltıldı',
            'update_error': 'Güncelleme hatası: ',
            'server_error': 'Sunucu hatası',
            'menu_update_error': 'Menü listesi güncelleme hatası',
            'branch_load_error': 'Şubeler yüklenirken hata: ',
            'branch_load_server_error': 'Şubeler yüklenirken hata: Sunucu hatası',
            'branch_changed': 'Şube değiştirildi: ',
            'branch_change_error': 'Şube değiştirme hatası: ',
            'cart_restore_error': 'Sepet geri yükleme hatası',
            'remove_error': 'Kaldırma hatası: ',
            'table_select_error': 'Lütfen bir masa seçin.',
            'order_submit_success': 'Sipariş başarıyla gönderildi',
            'order_submit_error': 'Gönderme hatası: ',
            'active_orders_error': 'Aktif siparişler yüklenirken hata: ',
            'completed_orders_error': 'Tamamlanan siparişler yüklenirken hata: ',
            'order_completed': 'Sipariş tamamlandı',
            'order_deleted': 'Sipariş silindi',
            'order_complete_error': 'Tamamlama hatası: ',
            'order_delete_error': 'Silme hatası: ',
            'new_order_received': 'Yeni sipariş #',
            'title': 'Künye',
            'responsible_text': 'Bu web sitesinin ana sayfası ve diğer bilgi sayfalarından (çevrimiçi mağazalar hariç) sorumlu olan:',
            'manager_label': 'Yönetici: %s',
            'tax_label': 'Vergi Numarası: %s',
            'court_label': 'Yetkili Mahkeme: %s',
            'register_label': 'Ticaret Sicili: %s',
            'vat_label': 'KDV: %s',
            'franchise_note': 'Her Sushi Yana şubesi bağımsız bir franchisee tarafından işletilir. Bu kişi, kendi işletmesi için ürünlerin üretiminden ve teslimatından sorumludur. Teslimatla ilgili sorularınız veya endişeleriniz varsa, lütfen yukarıdaki listede bulabileceğiniz ilgili şubenin sorumlusuyla iletişime geçin.',
            'contact_note': 'Franchise merkezimize şu e-posta adresinden ulaşabilirsiniz',
            'contact_email': 'buero@sushi-yana.de',
            'contact_instruction': 'Lütfen bunu yalnızca genel sorular için kullanın.'
        },
        'es': {
            'impressum': 'Aviso legal',
            'datenschutz': 'Política de privacidad',
            'select_branch': 'Seleccionar sucursal:',
            'select_language': 'Seleccionar idioma:',
            'footer_copyright': 'Sushi Yana © Todos los derechos reservados. 2025',
            'no_items_found': 'No se encontraron artículos.',
            'allergens_additives': 'Alérgenos y aditivos',
            'ingredients': 'Ingredientes',
            'page_title': 'Menú digital',
            'sushi_page_title': 'Menú digital - Sushi',
            'vegetarian_page_title': 'Menú digital - Vegetariano',
            'yana_menu_page_title': 'Menú digital - Menú Yana',
            'warmekueche_menu_page_title': 'Menú digital - Menú Cocina Caliente',
            'cart_page_title': 'Menú digital - Carrito',
            'invoice_page_title': 'Menú digital - Factura',
            'soup': 'Sopa',
            'starter': 'Entrante',
            'gyoza': 'Gyoza',
            'salad': 'Ensalada',
            'summer_roll': 'Rollo de verano',
            'sushi': 'Sushi',
            'warm_kitchen': 'Cocina caliente',
            'bowl': 'Tazón',
            'dessert': 'Postre',
            'extras': 'Extras',
            'drinks': 'Bebidas',
            'warm_drinks': 'Bebidas calientes',
            'menus': 'Menús',
            'sashimi': 'Sashimi',
            'makis': 'Makis',
            'inside_out_rolls': 'Rollos invertidos',
            'mini_yana_rolls': 'Mini rollos Yana',
            'yana_rolls': 'Rollos Yana',
            'nigiris': 'Nigiris',
            'special_rolls': 'Rollos especiales',
            'temaki': 'Temaki',
            'vegetarian': 'Vegetariano',
            'chop_suey': 'Chop Suey',
            'peanut_dish': 'Plato de cacahuetes',
            'vegetables': 'Verduras',
            'mango_chutney': 'Chutney de mango',
            'noodles': 'Fideos',
            'red_curry': 'Curry rojo',
            'rice': 'Arroz',
            'sweet_sour_sauce': 'Salsa agridulce',
            'yellow_curry': 'Curry amarillo',
            'your_cart': 'Tu carrito',
            'cart_empty': 'Tu carrito está vacío.',
            'total_amount': 'Monto total',
            'confirm': 'Confirmar',
            'your_invoice': 'Tu factura - Confirmación de pedido',
            'number': 'Número',
            'item': 'Artículo',
            'quantity': 'Cantidad',
            'unit_price': 'Precio unitario',
            'subtotal': 'Subtotal',
            'inactivity_timer': 'Temporizador de inactividad: <span id="timer-countdown"></span>',
            'seconds': 'Segundos',
            'delete_return': 'Eliminar y regresar',
            'notify_staff': 'Notificar al personal',
            'order_placed': '¡Pedido realizado! Por favor, espera a que un miembro del personal te asista.',
            'select_your_table': 'Selecciona tu mesa:',
            'select_table': 'Seleccionar mesa',
            'table_n': 'Mesa %d',
            'language_changed': 'Idioma cambiado a ',
            'item_removed': 'Artículo eliminado',
            'quantity_increased': 'Cantidad aumentada',
            'quantity_decreased': 'Cantidad reducida',
            'update_error': 'Error al actualizar: ',
            'server_error': 'Error del servidor',
            'menu_update_error': 'Error al actualizar la lista del menú',
            'branch_load_error': 'Error al cargar sucursales: ',
            'branch_load_server_error': 'Error al cargar sucursales: Error del servidor',
            'branch_changed': 'Sucursal cambiada a ',
            'branch_change_error': 'Error al cambiar la sucursal: ',
            'cart_restore_error': 'Error al restaurar el carrito',
            'remove_error': 'Error al eliminar: ',
            'table_select_error': 'Por favor, selecciona una mesa.',
            'order_submit_success': 'Pedido enviado con éxito',
            'order_submit_error': 'Error al enviar: ',
            'active_orders_error': 'Error al cargar pedidos activos: ',
            'completed_orders_error': 'Error al cargar pedidos completados: ',
            'order_completed': 'Pedido completado',
            'order_deleted': 'Pedido eliminado',
            'order_complete_error': 'Error al completar: ',
            'order_delete_error': 'Error al eliminar: ',
            'new_order_received': 'Nuevo pedido #',
            'title': 'Aviso legal',
            'responsible_text': 'Responsable de la página de inicio y de las páginas de información adicionales (excluyendo las tiendas en línea) de este sitio web es:',
            'manager_label': 'Gerente: %s',
            'tax_label': 'Número fiscal: %s',
            'court_label': 'Tribunal competente: %s',
            'register_label': 'Registro mercantil: %s',
            'vat_label': 'IVA: %s',
            'franchise_note': 'Cada sucursal de Sushi Yana es operada por un franquiciado independiente. Este es responsable de la producción y entrega de sus productos para su propio negocio. Si tienes preguntas o inquietudes sobre tu entrega, por favor contacta al responsable de la sucursal correspondiente, que puedes encontrar en la lista anterior.',
            'contact_note': 'Puedes contactar con nuestra central de franquicias a través de la dirección de correo electrónico',
            'contact_email': 'buero@sushi-yana.de',
            'contact_instruction': 'Por favor, utiliza esto exclusivamente para consultas generales.'
        },
        'ar': {
            'impressum': 'بيانات الشركة',
            'datenschutz': 'سياسة الخصوصية',
            'select_branch': 'اختر الفرع:',
            'select_language': 'اختر اللغة:',
            'footer_copyright': 'سوشي يانا © جميع الحقوق محفوظة. 2025',
            'no_items_found': 'لم يتم العثور على عناصر.',
            'allergens_additives': 'المواد المسببة للحساسية والإضافات',
            'ingredients': 'المكونات',
            'page_title': 'قائمة طعام رقمية',
            'sushi_page_title': 'قائمة طعام رقمية - سوشي',
            'vegetarian_page_title': 'قائمة طعام رقمية - نباتي',
            'yana_menu_page_title': 'قائمة طعام رقمية - قائمة يانا',
            'warmekueche_menu_page_title': 'قائمة طعام رقمية - قائمة المطبخ الدافئ',
            'cart_page_title': 'قائمة طعام رقمية - سلة التسوق',
            'invoice_page_title': 'قائمة طعام رقمية - فاتورة',
            'soup': 'شوربة',
            'starter': 'مقبلات',
            'gyoza': 'جيوزا',
            'salad': 'سلطة',
            'summer_roll': 'لفائف الصيف',
            'sushi': 'سوشي',
            'warm_kitchen': 'مطبخ دافئ',
            'bowl': 'وعاء',
            'dessert': 'حلوى',
            'extras': 'إضافات',
            'drinks': 'مشروبات',
            'warm_drinks': 'مشروبات دافئة',
            'menus': 'قوائم',
            'sashimi': 'ساشيمي',
            'makis': 'ماكي',
            'inside_out_rolls': 'لفائف من الداخل إلى الخارج',
            'mini_yana_rolls': 'لفائف يانا الصغيرة',
            'yana_rolls': 'لفائف يانا',
            'nigiris': 'نيغيري',
            'special_rolls': 'لفائف خاصة',
            'temaki': 'تيماكي',
            'vegetarian': 'نباتي',
            'chop_suey': 'تشوب سوي',
            'peanut_dish': 'طبق الفول السوداني',
            'vegetables': 'خضروات',
            'mango_chutney': 'تشاتني المانجو',
            'noodles': 'نودلز',
            'red_curry': 'كاري أحمر',
            'rice': 'أرز',
            'sweet_sour_sauce': 'صلصة حلوة وحامضة',
            'yellow_curry': 'كاري أصفر',
            'your_cart': 'سلة التسوق الخاصة بك',
            'cart_empty': 'سلة التسوق الخاصة بك فارغة.',
            'total_amount': 'المبلغ الإجمالي',
            'confirm': 'تأكيد',
            'your_invoice': 'فاتورتك - تأكيد الطلب',
            'number': 'رقم',
            'item': 'عنصر',
            'quantity': 'الكمية',
            'unit_price': 'سعر الوحدة',
            'subtotal': 'المجموع الفرعي',
            'inactivity_timer': 'مؤقت الخمول: <span id="timer-countdown"></span>',
            'seconds': 'ثوانٍ',
            'delete_return': 'حذف والعودة',
            'notify_staff': 'إعلام الموظفين',
            'order_placed': 'تم تقديم الطلب! يرجى الانتظار حتى يساعدك أحد الموظفين.',
            'select_your_table': 'اختر طاولتك:',
            'select_table': 'اختر طاولة',
            'table_n': 'طاولة %d',
            'language_changed': 'تم تغيير اللغة إلى ',
            'item_removed': 'تم إزالة العنصر',
            'quantity_increased': 'تم زيادة الكمية',
            'quantity_decreased': 'تم تقليل الكمية',
            'update_error': 'خطأ أثناء التحديث: ',
            'server_error': 'خطأ في الخادم',
            'menu_update_error': 'خطأ أثناء تحديث قائمة الطعام',
            'branch_load_error': 'خطأ أثناء تحميل الفروع: ',
            'branch_load_server_error': 'خطأ أثناء تحميل الفروع: خطأ في الخادم',
            'branch_changed': 'تم تغيير الفرع إلى ',
            'branch_change_error': 'خطأ أثناء تغيير الفرع: ',
            'cart_restore_error': 'خطأ أثناء استعادة السلة',
            'remove_error': 'خطأ أثناء الإزالة: ',
            'table_select_error': 'يرجى اختيار طاولة.',
            'order_submit_success': 'تم تقديم الطلب بنجاح',
            'order_submit_error': 'خطأ أثناء التقديم: ',
            'active_orders_error': 'خطأ أثناء تحميل الطلبات النشطة: ',
            'completed_orders_error': 'خطأ أثناء تحميل الطلبات المكتملة: ',
            'order_completed': 'تم إكمال الطلب',
            'order_deleted': 'تم حذف الطلب',
            'order_complete_error': 'خطأ أثناء الإكمال: ',
            'order_delete_error': 'خطأ أثناء الحذف: ',
            'new_order_received': 'طلب جديد #',
            'title': 'بيانات الشركة',
            'responsible_text': 'المسؤول عن الصفحة الرئيسية وصفحات المعلومات الإضافية (باستثناء المتاجر عبر الإنترنت) لهذا الموقع هو:',
            'manager_label': 'المدير: %s',
            'tax_label': 'رقم الضريبة: %s',
            'court_label': 'المحكمة المختصة: %s',
            'register_label': 'السجل التجاري: %s',
            'vat_label': 'ضريبة القيمة المضافة: %s',
            'franchise_note': 'كل فرع من فروع سوشي يانا يديره صاحب امتياز مستقل. وهو مسؤول عن إنتاج وتسليم منتجاته لأعماله الخاصة. إذا كانت لديك أسئلة أو مخاوف بشأن تسليمك، يرجى التواصل مع المسؤول عن الفرع المعني، والذي يمكنك العثور عليه في القائمة أعلاه.',
            'contact_note': 'يمكنك التواصل مع مقر الامتياز الخاص بنا عبر عنوان البريد الإلكتروني',
            'contact_email': 'buero@sushi-yana.de',
            'contact_instruction': 'يرجى استخدام هذا فقط للاستفسارات العامة.'
        }
    };

    // Get initial language from body data attribute (set by PHP from session)
    let currentLang = document.body.dataset.lang || 'de';

    // Update static text based on language, preserving inner HTML where needed
    function updateStaticText(lang) {
        document.querySelectorAll('[data-translate]').forEach(element => {
            const key = element.getAttribute('data-translate');
            const args = element.getAttribute('data-translate-args');
            if (translations[lang] && translations[lang][key]) {
                let text = translations[lang][key];
                if (args && key === 'table_n') {
                    text = text.replace('%d', args);
                    element.textContent = text;
                } else if (key === 'inactivity_timer') {
                    // Use innerHTML to preserve the <span id="timer-countdown">
                    element.innerHTML = text;
                } else {
                    element.textContent = text;
                }
            }
        });
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
    }

    // Expose translation function globally
    window.getTranslation = function(key, lang) {
        return translations[lang] && translations[lang][key] ? translations[lang][key] : key;
    };

    // Refresh dynamic content based on branch and language
    function refreshContent(branch, lang) {
        const page = document.body.dataset.page;
        const table = document.body.dataset.table;
        const $content = $('.content');

        if (['yana_menu', 'warmekueche_menu', 'sushi_menu'].includes(page)) {
            $content.html('<div class="loading-spinner" style="display: block;"></div>');
            $.ajax({
                url: BASE_PATH + 'config/artikelliste.php',
                type: 'POST',
                data: { table: table, filiale: branch, lang: lang },
                success: function(data) {
                    $content.html(data);
                    updateStaticText(lang);
                    if (typeof attachArtikellisteListeners === 'function') {
                        attachArtikellisteListeners();
                    } else {
                        console.error('attachArtikellisteListeners is not defined');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    $content.html('<p>Error loading content.</p>');
                }
            });
        } else if (page === 'index') {
            const $menuGrid = $('#menu-grid');
            $menuGrid.html('<div class="loading-spinner" style="display: block;"></div>').addClass('loading').attr('aria-busy', 'true');
            $.ajax({
                url: BASE_PATH + 'config/load_menu.php',
                type: 'GET',
                data: { lang: lang },
                success: function(data) {
                    $menuGrid.html(data).removeClass('loading').attr('aria-busy', 'false');
                    updateStaticText(lang);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    $menuGrid.html('<p>Error loading menu.</p>').removeClass('loading').attr('aria-busy', 'false');
                }
            });
        } else if (page === 'sushi') {
            const $menuGrid = $('#menu-grid');
            $menuGrid.html('<div class="loading-spinner" style="display: block;"></div>').addClass('loading').attr('aria-busy', 'true');
            $.ajax({
                url: BASE_PATH + 'config/load_sushi_menu.php',
                type: 'GET',
                data: { lang: lang },
                success: function(data) {
                    $menuGrid.html(data).removeClass('loading').attr('aria-busy', 'false');
                    updateStaticText(lang);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    $menuGrid.html('<p>Error loading menu.</p>').removeClass('loading').attr('aria-busy', 'false');
                }
            });
        } else if (page === 'warmekueche') {
            const $menuGrid = $('#menu-grid');
            $menuGrid.html('<div class="loading-spinner" style="display: block;"></div>').addClass('loading').attr('aria-busy', 'true');
            $.ajax({
                url: BASE_PATH + 'config/load_warmekueche_menu.php',
                type: 'GET',
                data: { lang: lang },
                success: function(data) {
                    $menuGrid.html(data).removeClass('loading').attr('aria-busy', 'false');
                    updateStaticText(lang);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    $menuGrid.html('<p>Error loading menu.</p>').removeClass('loading').attr('aria-busy', 'false');
                }
            });
        } else if (['cart', 'final_order', 'sushi_vegetarisch'].includes(page)) {
            updateStaticText(lang);
        } else if (page === 'datenschutz') {
            const $content = $('.legal-content');
            $content.html('<div class="loading-spinner" style="display: block;"></div>');
            $.ajax({
                url: BASE_PATH + 'config/api.php',
                type: 'POST',
                dataType: 'html',
                data: { action: 'get_translated_page', page: 'datenschutz', lang: lang },
                success: function(data) {
                    $content.html(data);
                    updateStaticText(lang);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    $content.html('<p>Error loading content.</p>');
                }
            });
        } else if (page === 'impressum') {
            const $content = $('.legal-content');
            const branch = branchDropdown && branchDropdown.value ? branchDropdown.value : 'neukoelln';
            $content.html('<div class="loading-spinner" style="display: block;"></div>');
            $.ajax({
                url: BASE_PATH + 'config/api.php',
                type: 'GET',
                dataType: 'json',
                data: { action: 'get_branch_impressum', branch: branch, lang: lang },
                success: function(response) {
                    if (response.status === 'success') {
                        const branchInfo = response.branch_info;
                        // Helper function to safely get translation with fallback
                        const t = (key, fallback = '') => translations[lang] && translations[lang][key] ? translations[lang][key] : fallback;
                        // Construct HTML with proper substitution and formatting
                        const html = `
                            <h1 data-translate="title">${t('title', 'Impressum')}</h1>
                            <p data-translate="responsible_text"><strong>${t('responsible_text', 'Responsible for this website:')}</strong></p>
                            <p class="address-block">${branchInfo.name || 'N/A'}</p>
                            <p class="address-block">${branchInfo.address ? branchInfo.address.replace(/\n/g, '<br>') : 'N/A'}</p>
                            <p><a href="mailto:${branchInfo.email || ''}" class="email-link">${branchInfo.email || 'N/A'}</a></p>
                            <p>${t('manager_label', 'Manager: %s').replace('%s', branchInfo.manager || 'N/A')}</p>
                            <p>${t('tax_label', 'Tax Number: %s').replace('%s', branchInfo.tax_number || 'N/A')}</p>
                            ${branchInfo.court ? `<p>${t('court_label', 'Court: %s').replace('%s', branchInfo.court)}</p>` : ''}
                            ${branchInfo.register ? `<p>${t('register_label', 'Register: %s').replace('%s', branchInfo.register)}</p>` : ''}
                            ${branchInfo.vat ? `<p>${t('vat_label', 'VAT: %s').replace('%s', branchInfo.vat)}</p>` : ''}
                            <p data-translate="franchise_note">${t('franchise_note', 'Franchise note unavailable')}</p>
                            <p data-translate="contact_note">${t('contact_note', 'Contact our headquarters at')} <a href="mailto:${t('contact_email', 'buero@sushi-yana.de')}" class="email-link">${t('contact_email', 'buero@sushi-yana.de')}</a>. ${t('contact_instruction', 'For general inquiries only')}</p>
                        `;
                        $content.html(html);
                        updateStaticText(lang); // Update any static translations
                    } else {
                        $content.html('<p>Error loading branch data.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    $content.html('<p>Error loading content.</p>');
                }
            });
        }
    }

    // Language dropdown handler
    if (languageDropdown) {
        // Set dropdown to session language on load
        languageDropdown.value = currentLang;

        $(languageDropdown).off('change').on('change', function() {
            if (isLanguageChanging) return;
            isLanguageChanging = true;

            const newLang = this.value;
            const branch = branchDropdown && branchDropdown.value ? branchDropdown.value : 'neukoelln';

            $.ajax({
                url: BASE_PATH + 'config/api.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'set_language', language: newLang },
                success: function(response) {
                    if (response.status === 'success') {
                        currentLang = newLang;
                        document.body.dataset.lang = newLang;
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('lang', newLang);
                        window.history.pushState({}, document.title, currentUrl.toString());
                        refreshContent(branch, newLang);
                        if (typeof showToast === 'function') {
                            const langName = {
                                'de': 'Deutsch',
                                'en': 'English',
                                'fr': 'Français',
                                'pl': 'Polski',
                                'it': 'Italiano',
                                'ru': 'Русский',
                                'tr': 'Türkçe',
                                'es': 'Español',
                                'ar': 'العربية'
                            }[newLang] || newLang;
                            showToast('language_changed', false, langName);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error setting language:', status, error);
                },
                complete: function() {
                    isLanguageChanging = false;
                }
            });
        });
    } if (branchDropdown) {
        $(branchDropdown).off('change').on('change', function() {
            const newBranch = this.value;
            $.ajax({
                url: BASE_PATH + 'config/api.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'set_branch', branch: newBranch },
                success: function(response) {
                    if (response.status === 'success') {
                        refreshContent(newBranch, currentLang);
                        if (typeof showToast === 'function') {
                            showToast('branch_changed', false, newBranch);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error setting branch:', status, error);
                }
            });
        });
    } else {
        console.warn('Branch dropdown not found on this page');
    }

    // Handle back/forward navigation
    window.addEventListener('popstate', function(event) {
        const url = new URL(window.location.href);
        const lang = url.searchParams.get('lang') || currentLang;
        const branch = branchDropdown && branchDropdown.value ? branchDropdown.value : 'neukoelln';
        currentLang = lang;
        document.body.dataset.lang = lang;
        if (languageDropdown) languageDropdown.value = lang;
        refreshContent(branch, lang);
    });

    // Initial setup: Use session language and sync everything
    const branch = branchDropdown && branchDropdown.value ? branchDropdown.value : 'neukoelln';
    updateStaticText(currentLang);
    refreshContent(branch, currentLang);
});