<p>Hi,<br>
<?php echo ($network['CreatedByUser']['firstname'])? $network['CreatedByUser']['firstname'] .' '. $network['CreatedByUser']['lastname'] .' &lt;'. $network['CreatedByUser']['email'] .'&gt;': $network['CreatedByUser']['email'] ; ?> sent you an invitation to join him on Delivrd inventory &amp; order management software.</p>
<p>You will join his <strong><?php echo $network['Network']['name']; ?></strong> network.</p>
<p>Click the link below to sign up to Delivrd and join his network.</p>
<p>If you are already a Delivrd user, you can accept the invitation after you log in to Delivrd.</p>
<p>Invitation link: <a href="<?php echo $this->Html->url(array('controller' => 'networks', 'action' => 'signup', $hash), true); ?>">Click to Accept invitation</a></p>
<br>
<p>The Delivrd team.<br>
delivrd.com</p>