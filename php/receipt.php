<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';

    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
    }


?>

<h1>Ajouter / modifier un ticket</h1>

<form method="post" name="receipt">
    <fieldset>
    <div class="form-group row">
      <label for="photo">Prendre une photo</label>
      <input type="file" accept="image/*" class="form-control-file photoReceipt" name = "photo" id="photo"  onchange="loadFile(event)">
    </div>

    <div class="form-group row">
        <img id="preload">
    </div>
    
    <div class="form-group row">
      <label for="date">Date</label>
      <input type="date" class="form-control" id="date" name="date"  value="<?= date("Y-m-d") ?>" required>
    </div>
    
    <div class="form-group row">
      <label for="montant">Montant TTC</label>
      <input type="number" class="form-control" id="montantTTC" name="montantTTC" placeholder="Saisir le montant TTC" required>
    </div>    

    <fieldset class="form-group">
      <legend>TVA</legend>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva1" value="tva1">
          5.5
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva2" value="tva2" checked="">
          10
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva3" value="tva3">
          20
        </label>
      </div>
    </fieldset>

    <fieldset class="form-group">
      <legend>Pointé</legend>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="checked" id="oui" value="oui">
          oui
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="checked" id="non" value="non" checked="">
          non
        </label>
      </div>
    </fieldset>

    <div class="form-group">
      <label for="exampleTextarea">Description</label>
      <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Valider</button>

</form>


<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
