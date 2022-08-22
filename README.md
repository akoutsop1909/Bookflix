# Bookflix
Ομαδική εργασία στα πλαίσια του μαθήματος "Μηχανική Λογισμικού για Διαδικτυακές Εφαρμογές" του ΠΜΣ "Ευφυείς Τεχνολογίες Διαδικτύου", στην οποία ασχολήθηκα με το κομμάτι της JavaScript (AngularJS).

Πρόκειται για μια διαδικτυακή βιβλιοθήκη, μέσω της οποίας ένας συνδεδεμένος χρήστης δανείζεται, διαβάζει, επιστρέφει ή/και προσθέτει βιβλία στα αγαπημένα του. Ο δανεισμός και η προσθήκη βιβλίων στα αγαπημένα πραγματοποιείται μόνο από τη σελίδα με τις λεπτομέρειες του βιβλίου, ενώ οι υπόλοιπες διαδικασίες προσφέρονται και στο προφίλ του. Για έναν χρήστη που δεν είναι εξοικειωμένος με την εφαρμογή, η αρχική σελίδα παραθέτει προτάσεις βιβλίων μέσω του καρουζέλ, δημοφιλών κατηγοριών (Fantasy, Romance, Science Fiction, Thriller) και best sellers. Εναλλακτικά, πλοηγείται στα διαθέσιμα βιβλία από την καρτέλα "Library", όπου πρώτα επιλέγει μία και κατηγορία και στη συνέχεια "Show details" του βιβλίου που τον ενδιαφέρει. Απο εκεί, πληροφορείται για τις λεπτομέρειες του βιβλίου (συγγραφέας, σημειώσεις έκδοσης, περιγραφή και άλλα) και αποφασίζει αν θα το δανειστεί ή/και προσθέσει στα αγαπημένα του. Για την εξερεύνηση των βιβλίων δεν είναι απαραίτητη η δημιουργία λογαριασμού.

* Καλεί τα API "Books", "Covers", "Subjects" και "Works" του Open Library (https://openlibrary.org/developers/api)
* Δεν υλοποιεί το "Suggest this", αξιολόγηση βιβλίου, φόρμα επικοινωνίας και μπάρα αναζήτησης.
* Περισσότερα στο documentation. 

## Σχεδιάγραμμα Βάσης Δεδομένων
![Εικόνα1](https://user-images.githubusercontent.com/51194516/185928000-8898f0d9-1675-4195-b151-101a7f2688e3.png)

## Αρχιτεκτονική Συστήματος
![Screenshot_1](https://user-images.githubusercontent.com/51194516/185928402-959f68e3-8a3d-4738-9934-4d26b9cd841f.png)
