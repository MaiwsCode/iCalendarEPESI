# iCalendarEPESI
Synchronizacja kalendarza EPESI z kalendarzem Google
Instrukcja:
Folder iCalSync skopiuj do folderu modules EPESI
Plik z folderu Home dodaj do głownego katalogu Twojego WWW tak aby był do niego dostęp z http://TwojaDomena/oauth2callback.php
Zainstaluj aplikacje w Administrator -> Administracja modułami i Sklep
Twoja aplikacja jest dostępną pod Menu -> Moje ustawienia -> Panel sterowania
Potrzeba jeszcze kilku konfiguracji:
Przejdz do Google Console a następnie utwórz projekt
Teraz w zakładce "Dane logowania" kliknij Utwórz dane logowania i stwórz swoją aplikację - ID klienta OAuth
  Wybierz Aplikacja internetowa
  Nazwa może być dowolna - nie ma ona żadnego znaczenia w dalszych krokach
  W "Autoryzowane identyfikatory URI przekierowania" dodaj adres url tak aby był zgodny z dostępem do pliku oauth2callback.php
  Po utworzeniu kliknij pobierz JSON a następnie wklej plik do modules/iCalSync oraz zmień jego nazwe na: "client_secret.json"
Teraz możesz korzystać z modułu
