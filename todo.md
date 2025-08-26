## Must

- Remplacer la logique de "paiement à la livraison" par "retrait en magasin". Cela sous entend que si l'option "retrait en magasin" est sélectionnée on supprime les frais de livraison du montant de la commande. Les champs doivent être également renommés en bdd. Renommer le champ "is_completed" en "is_delivered dans Order. Ajour du champ shipping_cost dans Order. Pour savoir si le client a payé des frais de livraison ou a choisi le retrait en magasin (champ avec valeur "null" dans ce cacs de figure). Quid lorsque je change les frias de livraison d'une ville ? Ok avec le champ shipping cost mais pas ok sur le libellé. Idée : au lieu de stocker le city_id dans la table order, stocker le nom de la ville en string. Cela n'empêche pas de conserver la table city.

- Gérer l’affichage des commandes en conséquence des modifications précédentes.

- Faire en sorte que les informations sur la facture soient les informations générées au moment de la commande, de façon à avoir le prix et le produit (en cas de suppression ou modification) qui correspondent à la réalité. Création d'un nouveau champ (current_price dans la table order_products). Renommer product_history par product_stock_history. Renoomer également le form, l'entity eet le repository.

- Ajouter les "mentions légales" / "CGI" (dans le footer avec ouverture d'une modale).

- Installer Tailwind et Daisy Ui avec AssetMapper.



## Should

- Vérifier les menus visibles au niveau de la navbar (desktop et mobile).

- CSS facture

- CSS résultat de recherche

- CSS barre de recherche

- CSS paginator

- Ajouter une bulle au niveau du panier pour connaitre le nombre d'articles qui sont dedans (ne pas afficher, si la quantité de produit dans le panier est inférieure à 1)



## Could

- Harmoniser le nom des fonctions

- Harmoniser les url des path

- Harmoniser le nom des variables

- Donner des noms cohérents à mes regions

- Bien indenter le code pour avoir une structure claire et lisible



## Won't

- Implémenter avec de vraies données (erase bdd avec raz des id)
