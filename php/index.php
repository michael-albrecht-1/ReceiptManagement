<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';

    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
    }
?>


<h1>Liste des tickets</h1>

<?php 
    $showReceipts = mysqli_query($conn, "SELECT * FROM receipts");
?>
<table class="table table-hover">
<thead>
    <tr class="table-dark">
      <th scope="col">Date</th>
      <th scope="col">TVA</th>
      <th scope="col">Montant</th>
      <th scope="col">Description</th>
    </tr>
  </thead>
<?php
    while ($row = mysqli_fetch_array($showReceipts)) {
        echo "<tr>";
            echo "<td>".$row['date_emission']."</td>";
            echo "<td>".$row['tva']."</td>";
            echo "<td>".$row['montant_ttc']."</td>";
            echo "<td>".$row['description']."</td>";
        echo "</tr>";
    }
?>
</table>


<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
