<?php
    include("config.php");
    $topic = $_POST['topic'];
    $idTopic = $_POST['idTopic'];
    $req1 = $conn -> prepare("select commentaire.texte, commentaire.dateajoutcom, utilisateur.pseudo, commentaire.idcom 
                                    from commentaire left join topic using(idtopic) left join utilisateur 
                                    on commentaire.mailcom=utilisateur.mail where idtopic ='$idTopic' order by dateajoutcom");
    $req1 -> execute();

    $select = '';

    if($req1 -> rowCount() > 0) {
        while($data1 = $req1 -> fetch(PDO::FETCH_ASSOC)) {
            $TxtComTmp = $data1['texte'];
            $DateComTmp = $data1['dateajoutcom'];
            $PseudoUtilTmp = $data1['pseudo'];
            $IdCom = $data1['idcom'];
            $NbLikes = 0;

            $req2 = $conn -> prepare("select commentaire.idcom, count(*) from commentaire join likecom using(idcom) group by idcom");
            $req2 -> execute();
            if($req2 -> rowCount() > 0) {
                while($data2 = $req2 -> fetch(PDO::FETCH_ASSOC)) {
                    if($data2['idcom'] == $data1['idcom']) {
                        $NbLikes = $data2['count(*)'];
                        break;
                    }
                }
            }

            $select .= '<div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h3><div class ="pl-3 pt-1 pr-3">' . $PseudoUtilTmp .'</div></h3>
                                </div>
                                <div class="card-text">
                                    <div class ="pl-3 pt-1 pr-3">' . $TxtComTmp .'</div>
                                    <footer class="blockquote-footer">
                                        <div class ="pl-3 pt-1 pr-3">' . $DateComTmp .'</div>
                                    </footer>
                                    <a href="php/like.php?gIdCom=' . $IdCom . '&gTopic=' . $topic . '&gIdTopic='.$idTopic.'">Likes ' . $NbLikes . '</a>
                                    <form  method ="post" class="form-check-inline" style="float:right;">
                                        <button style="float:right;" name="supprimer" value=' . $IdCom . '> Supprimer </button>
                                    </form>
                                </div>
                            </div>
                        </div>';
        }
    }

    if (isset($_POST['supprimer'])) {
        $IdCom = $_POST['supprimer'];
        if($_SESSION['grade'] >= 2) {
            $req1 = $conn->prepare("delete from commentaire where idcom = '$IdCom'");
            $req1 -> execute();
        } else {
            echo "<script>
                      alert(\"Vous n'êtes pas autorisé à supprimer ce commentaire.\");
                  </scrypt>";
        }
        header("Location: displayCommentaires.php?gTopic=" . $topic . "&gIdTopic=" . $idTopic);
    }
    echo $select;
    $conn = null;
?>
