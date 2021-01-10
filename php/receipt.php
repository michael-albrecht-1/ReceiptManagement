<?php
    require __DIR__ . '/inc/header.tpl.php';
    require __DIR__ . '/config.php';

    // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
    }
?>

<h1>Ajouter / modifier un ticket</h1>

<form data-bitwarden-watching="1">
    <fieldset>
    <div class="form-group row">
      <button class="btn btn-primary"><label for="photo">Prendre une photo</button></p>
      <input type="file" accept="image/*" class="form-control-file photoReceipt" name = "photo" id="photo"  onchange="loadFile(event)">
    </div>

    <div class="form-group row">
        <img id="preload">
    </div>
    
    <div class="form-group row">
      <label for="date">Date</label>
      <input type="text" class="form-control" id="date" placeholder="Saisir la Date">
    </div>
    
    <div class="form-group row">
      <label for="montant">Montant TTC</label>
      <input type="text" class="form-control" id="montant" placeholder="Saisir le montant TTC">
    </div>    

    <div class="form-group row">
      <label for="tva">Taux de TVA</label>
      <input type="text" class="form-control" id="tva" placeholder="Saisir le taux de TVA">
    </div>    

    <fieldset class="form-group">
      <legend>TVA</legend>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva1" value="tva1" checked="">
          5.5
        </label>
      </div>
      <div class="form-check">
        <label class="form-check-label">
          <input type="radio" class="form-check-input" name="tva" id="tva2" value="tva2">
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
      <textarea class="form-control" id="description" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Valider</button>
    <button type="submit" class="btn btn-primary">Suivant</button>

</form>


<?php
    require __DIR__ . '/inc/footer.tpl.php';
?>
