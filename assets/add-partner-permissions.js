const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index

        );
    addTagFormDeleteLink(item);

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;

};
const addTagFormDeleteLink = (item) => {
    const removeFormButton = document.createElement('button');
    removeFormButton.innerHTML = '<i class="fas fa-trash text-sm text-red-600"> Suprimer';

    item.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the tag form
        item.remove();
    });
}

document
    .querySelectorAll('.js-btn-new')
    .forEach(btn => {
        btn.addEventListener("click", addFormToCollection)
    });

document
    .querySelectorAll('.js-btn-remove')
    .forEach((permissionForm) => {

        addTagFormDeleteLink(permissionForm)
    })
