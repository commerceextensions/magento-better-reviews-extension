To show grouped product reviews in the category product list, one small template file change is necessary. We change this function in the template file because we did not want to override the entire catalog product list for a small change.

In catalog/product/list.phtml find the lines that say <?php if($_product->getRatingSummary()): ?>
This code appears twice in this file. Replace both instances of this code with the following line:
<?php if($_product->getRatingSummary() || Mage::helper('betterreviews')->canShowAssociatedReviews($_product->getTypeId())): ?>

Thats all there is to it.
