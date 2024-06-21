window.imm = {
    edit(nodeId) {
        const parent = document.getElementById('node-' + nodeId);
        const text = window
            .prompt('Enter the title:', parent.firstElementChild.innerText ?? '')
            .replace(/(^ +)|( $)/ug, '');
        const description = window
            .prompt('Enter the description:', parent.firstElementChild.getAttribute('title') ?? '')
            .replace(/(^ +)|( $)/ug, '');
        const changes = {};
        if (text === '') {
            if (parent.parentElement.parentElement === document.body) {
                return;
            }
            parent.parentElement.removeChild(parent);
            fetch(location.href  + '/node/' + nodeId, {
                method: 'DELETE',
            });
            return;
        }
        if (text !== parent.firstElementChild.innerText) {
            changes.text = text;
            parent.firstElementChild.innerText = text;
        }
        if (description !== parent.firstElementChild.getAttribute('title')) {
            changes.description = description;
            parent.firstElementChild.setAttribute('title', description);
        }
        if (Object.keys(changes).length > 0) {
            fetch(location.href  + '/node/' + nodeId, {
                method: 'PATCH',
                body: JSON.stringify(changes),
            });
        }
    },
    async add(parentId) {
        const parent = document.getElementById('node-' + parentId);
        const text = window
            .prompt('Enter the title:', '')
            .replace(/(^ +)|( $)/ug, '');
        const description = window
            .prompt('Enter the description:', '')
            .replace(/(^ +)|( $)/ug, '');
        const data = await fetch(location.href  + '/parent/' + parentId, {
            method: 'PUT',
            body: JSON.stringify({
                text,
                description
            }),
        });
        if (data.status === 200) {
            const node = await data.json();
            const li = document.createElement('li');
            li.setAttribute('id', 'node-' + node.uuid);
            li.setAttribute('data-uuid', node.uuid);
            const span = document.createElement('span');
            span.innerText = node.text;
            span.setAttribute('title', node.description ?? '');
            span.setAttribute('onclick', "window.imm.edit('" + node.uuid + "')");
            li.appendChild(span);
            const ul = document.createElement('ul');
            const ili = document.createElement('li');
            const button = document.createElement('button');
            button.innerText = '+';
            button.setAttribute('id', 'add-' + node.uuid);
            button.setAttribute('type', 'button');
            button.setAttribute('onclick', "window.imm.add('" + node.uuid + "')");
            ili.appendChild(button);
            ul.appendChild(ili);
            li.appendChild(ul);
            parent.lastElementChild.insertBefore(li, parent.lastElementChild.lastElementChild);
        }
    },
    async load(nodeId) {
        const parent = document.getElementById('node-' + nodeId);
        parent.classList.remove('unloaded');
        parent.classList.add('loading');
        const data = await fetch(location.href  + '/parent/' + nodeId);
        if (data.status === 200) {
            const list = await data.json();
            if (Array.isArray(list)) {
                const ul = document.createElement('ul');
                for (const node of list) {
                    const li = document.createElement('li');
                    li.setAttribute('id', 'node-' + node.uuid);
                    li.setAttribute('data-uuid', node.uuid);
                    const span = document.createElement('span');
                    span.innerText = node.text;
                    span.setAttribute('title', node.description ?? '');
                    span.setAttribute('onclick', "window.imm.edit('" + node.uuid + "')");
                    li.appendChild(span);
                    ul.appendChild(li);
                }
                const li = document.createElement('li');
                const button = document.createElement('button');
                button.innerText = '+';
                button.setAttribute('id', 'add-' + nodeId);
                button.setAttribute('type', 'button');
                button.setAttribute('onclick', "window.imm.add('" + nodeId + "')");
                li.appendChild(button);
                ul.appendChild(li);
                parent.appendChild(ul);
                parent.classList.remove('loading');
                for (const node of list) {
                    window.imm.load(node.uuid);
                }
            }
        }
    }
};
