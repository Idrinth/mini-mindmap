window.imm = {
    since: new Date(),
    paused: false,
    loading: 0,
    createAddButton(uuid) {
        const button = document.createElement('button');
        button.innerText = '+';
        button.setAttribute('id', 'add-' + uuid);
        button.setAttribute('type', 'button');
        button.setAttribute('title', 'add new child node');
        button.setAttribute('onclick', "window.imm.add('" + uuid + "')");
        return button;
    },
    createContentSpan({text, description, uuid}) {
        const span = document.createElement('span');
        span.setAttribute('onclick', "window.imm.edit('" + uuid + "')");
        const info = document.createElement('strong');
        info.innerText = 'i';
        span.appendChild(info);
        const content = document.createElement('span');
        content.innerText = text;
        span.appendChild(content);
        const title = document.createElement('em');
        title.innerText = description ?? '';
        span.appendChild(title);
        return span;
    },
    createContentLi({text, description, uuid}) {
        const li = document.createElement('li');
        li.setAttribute('id', 'node-' + uuid);
        li.appendChild(window.imm.createContentSpan({text, description, uuid}));
        li.appendChild(document.createElement('ul'));
        li.lastElementChild.appendChild(document.createElement('li'));
        li.lastElementChild.lastElementChild.appendChild(window.imm.createAddButton(uuid));
        return li;
    },
    async update() {
        if (window.imm.paused || window.imm.loading > 0) {
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
                        el.firstElementChild.childNodes[0].setAttribute('class', node.description ? '' : 'hidden');
                        el.firstElementChild.childNodes[1].innerText = node.text;
                        el.firstElementChild.childNodes[2].innerText = node.description ?? '';
                    } else {
                        const parent = document.getElementById('node-'+node.parentUuid).lastElementChild;
                        parent.insertBefore(window.imm.createContentLi(node), parent.lastElementChild);
                    }
                }
            }
        }
        window.setTimeout(window.imm.update, 1000);
    },
    edit(nodeId) {
        const parent = document.getElementById('node-' + nodeId);
        const text = window
            .prompt('Enter the title:', parent.firstElementChild.childNodes[1].innerText ?? '')
            ?.replace(/(^ +)|( $)/ug, '');
        const description = window
            .prompt('Enter the description:', parent.firstElementChild.childNodes[1].innerText ?? '')
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
        if (text !== parent.firstElementChild.childNodes[1].innerText) {
            if (parent.parentElement.parentElement === document.body) {
                document.getElementsByTagName('h1')[0].innerText = text;
                document.getElementsByTagName('title')[0].innerText = text + ' | Idrinth Mini-Mindmap';
            }
            changes.text = text;
            parent.firstElementChild.innerText = text;
        }
        if (description !== parent.firstElementChild.childNodes[2].innerText) {
            changes.description = description;
            parent.firstElementChild.childNodes[2].innerText = description;
            parent.firstElementChild.childNodes[0].setAttribute('class', description ? '' : 'hidden');
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
            parent.lastElementChild.insertBefore(window.imm.createContentLi(await data.json()), parent.lastElementChild.lastElementChild);
        }
    },
    async load(nodeId) {
        window.imm.loading ++;
        const parent = document.getElementById('node-' + nodeId);
        const data = await fetch(location.href  + '/parent/' + nodeId);
        if (data.status === 200) {
            const list = await data.json();
            if (Array.isArray(list)) {
                const ul = document.createElement('ul');
                for (const node of list) {
                    const li = document.createElement('li');
                    li.setAttribute('id', 'node-' + node.uuid);
                    li.setAttribute('data-uuid', node.uuid);
                    li.appendChild(window.imm.createContentSpan(node));
                    ul.appendChild(li);
                }
                const li = document.createElement('li');
                li.appendChild(window.imm.createAddButton(nodeId));
                ul.appendChild(li);
                parent.appendChild(ul);
                for (const node of list) {
                    window.imm.load(node.uuid);
                }
            }
        }
        window.imm.loading --;
    }
};
window.addEventListener('blur', () => window.imm.paused = true);
window.addEventListener('focus', () => window.imm.paused = false);
