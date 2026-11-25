<?php // View: display only. Needs $user and $latestReports ?>
  <aside id="right-panel">

    <section id="profile">
      <img id="user-photo" src="<?= e($user['photo']) ?>" alt="Profile picture" width="72" height="72">
      <h2 id="user-name"><?= e($user['name']) ?></h2>
      <p>Rank: <span id="user-rank"><?= e($user['rank']) ?></span></p>
    </section>

    <section id="points-box">
      <h2>My points</h2>
      <p><strong id="user-points"><?= (int) $user['points'] ?></strong> points</p>
      <label for="rank-progress">Progress to next rank</label>
      <progress id="rank-progress" value="<?= (int) $user['progress'] ?>" max="100"></progress>
    </section>

    <section id="latest-reports">
      <h2>My latest reports</h2>
      <?php if (!$latestReports): ?>
        <p class="hint">No report yet.</p>
      <?php else: ?>
        <ul class="mini-reports">
          <?php foreach ($latestReports as $r): ?>
            <li>
              <span><?= e($r->getCategoryName()) ?></span>
              <span class="badge badge-<?= e($r->getStatus()) ?>"><?= e(str_replace('_', ' ', ucfirst($r->getStatus()))) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <a href="index.php?action=reports&amp;mine=1" class="see-all">See all my reports</a>
    </section>

  </aside>
