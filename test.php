<?php



// Die Nachricht
$nachricht = "Zeile 1\Zeile 2\Zeile 3";

// Falls eine Zeile der Nachricht mehr als 70 Zeichen enthälten könnte,
// sollte wordwrap() benutzt werden
$nachricht = wordwrap($nachricht, 70);

// Send
mail('s.hintersonnleitner@chello.at', 'Mein Betreff', $nachricht);


?>