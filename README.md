# iCalendarEPESI<br>
<h1>Synchronizacja kalendarza EPESI z kalendarzem Google <h1><br>
<b>Instrukcja:<b><br>
  Folder <span style='color:lightblue';>iCalSync</span> skopiuj do folderu modules EPESI<br>
Plik z folderu Home dodaj do głownego katalogu Twojego WWW tak aby był do niego dostęp z http://TwojaDomena/oauth2callback.php<br>
Zainstaluj aplikacje w Administrator -> Administracja modułami i Sklep<br>
Twoja aplikacja jest dostępną pod Menu -> Moje ustawienia -> Panel sterowania<br>
Potrzeba jeszcze kilku konfiguracji:<br>
Przejdz do Google Console a następnie utwórz projekt<br>
Teraz w zakładce "Dane logowania" kliknij Utwórz dane logowania i stwórz swoją aplikację - ID klienta OAuth<br>
  Wybierz Aplikacja internetowa<br>
  Nazwa może być dowolna - nie ma ona żadnego znaczenia w dalszych krokach<br>
  W "Autoryzowane identyfikatory URI przekierowania" dodaj adres url tak aby był zgodny z dostępem do pliku oauth2callback.php<br>
  Po utworzeniu kliknij pobierz JSON a następnie wklej plik do modules/iCalSync oraz zmień jego nazwe na: "client_secret.json"<br>
Teraz możesz korzystać z modułu<br>
