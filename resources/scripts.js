window.imm = {
    since: new Date(),
    paused: false,
    async update() {
        if (window.imm.paused) {
            window.setTimeout(window.imm.update, 100);
            return;
        }
        const since = Math.floor(window.imm.since.getTime() / 1000);
        window.imm.since = new Date();
        const data = await fetch(location.href  + '/since/' + since);
        if (data.status === 200) {
            const list = await data.json();
            if (Array.isArray(list)) {
                for (const node of list) {
                    const el = document.getElementById('node-'+node.uuid);
                    if (el) {
                        if (el.parentElement.parentElement === document.body) {
                            document.getElementsByTagName('h1')[0].innerText = node.text;
                            document.getElementsByTagName('title')[0].innerText = node.text + ' | Idrinth Mini-Mindmap';
                        }
                        el.firstElementChild.innerText = node.text;
                        el.firstElementChild.setAttribute('title', node.description ?? '');
                    } else {
                        const parent = document.getElementById('node-'+node.parentUuid).lastElementChild;
                        const li = document.createElement('li');
                        li.setAttribute('id', 'node-' + node.uuid);
                        li.setAttribute('data-uuid', node.uuid);
                        const span = document.createElement('span');
                        span.innerText = node.text;
                        span.setAttribute('title', node.description ?? '');
                        span.setAttribute('onclick', "window.imm.edit('" + node.uuid + "')");
                        li.appendChild(span);
                        li.appendChild(document.createElement('ul'));
                        li.lastElementChild.appendChild(document.createElement('li'));
                        const button = document.createElement('button');
                        button.innerText = '+';
                        button.setAttribute('id', 'add-' + node.uuid);
                        button.setAttribute('type', 'button');
                        button.setAttribute('onclick', "window.imm.add('" + node.uuid + "')");
                        li.lastElementChild.lastElementChild.appendChild(button);
                        parent.insertBefore(li, parent.lastElementChild);
                    }
                }
            }
        }
        window.setTimeout(window.imm.update, 1000);
    },
    edit(nodeId) {
        const parent = document.getElementById('node-' + nodeId);
        const text = window
            .prompt('Enter the title:', parent.firstElementChild.innerText ?? '')
            ?.replace(/(^ +)|( $)/ug, '');
        const description = window
            .prompt('Enter the description:', parent.firstElementChild.getAttribute('title') ?? '')
            ?.replace(/(^ +)|( $)/ug, '');
        if (text === '' || text === null) {
            if (parent.parentElement.parentElement === document.body) {
                return;
            }
            parent.parentElement.removeChild(parent);
            fetch(location.href  + '/node/' + nodeId, {
                method: 'DELETE',
            });
            return;
        }
        const changes = {};
        if (text !== parent.firstElementChild.innerText) {
            if (parent.parentElement.parentElement === document.body) {
                document.getElementsByTagName('h1')[0].innerText = text;
                document.getElementsByTagName('title')[0].innerText = text + ' | Idrinth Mini-Mindmap';
            }
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
                headers: {
                    'content-type': 'application/json',
                },
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
            headers: {
                'content-type': 'application/json',
            },
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
window.addEventListener('blur', () => window.imm.paused = true);
window.addEventListener('focus', () => window.imm.paused = false);
