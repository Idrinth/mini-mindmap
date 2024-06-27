window.imm = {
    since: new Date(),
    paused: false,
    loading: 0,
    async getEditedValues(defaultText = '', defaultDescription = '') {
        return new Promise((resolve) => {
            document.getElementById('text').value = defaultText;
            document.getElementById('description').value = defaultDescription;
            document.getElementById('node-modification').setAttribute('style', 'display:block');
            document.getElementById('node-modification').getElementsByTagName('button')[0].onclick = () => {
                document.getElementById('node-modification').setAttribute('style', 'display: none');                resolve({
                    text: defaultText,
                    description: defaultDescription,
                });
            }
            document.getElementById('node-modification').getElementsByTagName('button')[1].onclick = () => {
                document.getElementById('node-modification').setAttribute('style', 'display: none');
                const text = document.getElementById('text').value.trim();
                const description = document.getElementById('description').value.trim();
                resolve({
                    text,
                    description,
                });
            }
        });
    },
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
        span.setAttribute('class', description ? 'describes' : '');
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
    async edit(nodeId) {
        const parent = document.getElementById('node-' + nodeId);
        const {text, description} = await window.imm.getEditedValues(parent.firstElementChild.childNodes[1].innerText, parent.firstElementChild.childNodes[2].innerText)
        if (text === '') {
            if (parent.parentElement.parentElement === document.body) {
                return;
            }
            parent.parentElement.removeChild(parent);
            await fetch(location.href  + '/node/' + nodeId, {
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
            parent.firstElementChild.childNodes[1].innerText = text;
        }
        if (description !== parent.firstElementChild.childNodes[2].innerText) {
            changes.description = description;
            parent.firstElementChild.childNodes[2].innerText = description;
            parent.firstElementChild.setAttribute('class', description ? 'describes' : '');
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
        const {text, description} = await window.imm.getEditedValues();
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
                for (const node of list) {
                    parent.lastElementChild.insertBefore(this.createContentLi(node), parent.lastElementChild.lastElementChild);
                    window.setTimeout(() => window.imm.load(node.uuid), 0);
                }
            }
        }
        window.imm.loading --;
    }
};
window.addEventListener('blur', () => window.imm.paused = true);
window.addEventListener('focus', () => window.imm.paused = false);
