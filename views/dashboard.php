<h1>Mano Slaptažodžių Saugykla</h1>
<a href="index.php?logout=1" style="color: red;">Atsijungti</a>
<hr>

<h3>1. Generuoti naują slaptažodį</h3>
<form method="POST" action="index.php">
    Mažosios raidės: <input type="number" name="lc" value="2" min="0" required>
    Didžiosios raidės: <input type="number" name="uc" value="3" min="0" required>
    Skaičiai: <input type="number" name="num" value="2" min="0" required>
    Spec. simboliai: <input type="number" name="spec" value="2" min="0" required>
    <button type="submit" name="generate">Generuoti</button>
</form>

<?php if (!empty($generatedPassword)): ?>
    <p style="color: green; font-size: 18px;">
        Sugeneruotas slaptažodis: <b><?php echo htmlspecialchars($generatedPassword); ?></b>
    </p>
<?php endif; ?>

<hr>

<h3>2. Išsaugoti slaptažodį</h3>
<form method="POST" action="index.php">
    <p>
        Svetainės pavadinimas (pvz. Gmail):<br>
        <input type="text" name="title" required>
    </p>
    <p>
        Slaptažodis:<br>
        <!-- Jei slaptažodis buvo sugeneruotas, jis automatiškai įkris į šį laukelį -->
        <input type="text" name="gen_password" value="<?php echo !empty($generatedPassword) ? htmlspecialchars($generatedPassword) : ''; ?>" required>
    </p>
    <button type="submit" name="save_password">Saugoti į DB (Šifruoti)</button>
</form>

<hr>

<h3>3. Mano išsaugoti slaptažodžiai</h3>
<table border="1" cellpadding="10" cellspacing="0">
    <tr style="background-color: #f2f2f2;">
        <th>Svetainė</th>
        <th>Slaptažodis (Atkoduotas)</th>
        <th>Išsaugojimo data</th>
    </tr>
    <?php if (!empty($myPasswords)): ?>
        <?php foreach ($myPasswords as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['title']); ?></td>
                <td><?php echo htmlspecialchars($p['decrypted_password']); ?></td>
                <td><?php echo htmlspecialchars($p['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3">Kol kas neturite išsaugotų slaptažodžių.</td>
        </tr>
    <?php endif; ?>
</table>