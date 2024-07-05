window.imm = {
    since: new Date(),
    paused: false,
    rootUuid: null,
    loading: 0,
    mouseX: 0,
    mouseY: 0,
    drawArrows(nodeId) {
        const nodes = document.getElementById('node-'+nodeId)?.lastElementChild.children;
        if (! nodes || ! nodes.length) {
            return;
        }
        const source = window.imm.getBoundingClientRect(document.getElementById('node-'+nodeId).firstElementChild);
        for (let i = 0; i < nodes.length; i++) {
            const li= nodes.item(i);
            const uuid = li.getAttribute('data-uuid');
            //window.setTimeout(() => window.imm.drawArrows(uuid), 1);
            const arrow = document.getElementById('arrow-'+nodeId+'-'+uuid) ?? (() => {
                const arrow = document.createElement('div');
                arrow.classList.add('arrow');
                arrow.setAttribute('id', 'arrow-' + nodeId + '-' + uuid);
                document.body.insertBefore(arrow, document.getElementsByTagName('h1')[0].nextElementSibling);
                return arrow;
            })();
            const target = window.imm.getBoundingClientRect(li.firstElementChild);
            const originRight = source.left + source.width + 10;
            const originTop = source.top - source.height/2;
            const targetLeft = target.left - 10;
            const targetTop = target.top - target.height/2;
            const deltaX = targetLeft - originRight;
            const deltaY = targetTop - originTop;
            const deltaHyp = Math.sqrt(deltaX * deltaX + deltaY * deltaY + 2 * deltaX * deltaY);
            const degrees = Math.asin(deltaY / deltaHyp);
            arrow.setAttribute('style', 'transform: rotate(' + degrees + 'deg);height: '+deltaHyp+'px;left: '+(originRight+deltaX/2)+'px;top: '+originTop+'px;border-right: 1px solid darkgreen; transform-origin: '+originRight+'px '+originTop+'px;');
        }
    },
    getBoundingClientRect(element) {
        const parent = element.offsetParent ? imm.getBoundingClientRect(element.offsetParent) : {left: 0, top: 0, width: 0, height: 0};
        const rect = element.getBoundingClientRect();
        return {left: parent.left + element.offsetLeft, top: parent.top + element.offsetTop, width: rect.width, height: rect.height};
    },
    mouse(e) {
        window.imm.mouseX = e.pageX;
        window.imm.mouseY = e.pageY;
    },
    displayExport(value) {
        const text = document.createElement('textarea');
        text.addEventListener('click', (e) => {
            e.stopPropagation();
        });
        text.value = value;
        const wrapper = document.createElement('div');
        wrapper.setAttribute('style', 'display:block');
        wrapper.appendChild(text);
        wrapper.setAttribute('class', 'modal-backdrop');
        wrapper.addEventListener('click', () => document.body.removeChild(wrapper));
        document.body.appendChild(wrapper);
    },
    context(e) {
        if (document.getElementById('context-menu')) {
            document.body.removeChild(document.getElementById('context-menu'));
        }
        if (location.pathname.startsWith('/mindmap/') && e.target.localName === 'span') {
            e.preventDefault();
            const menu = document.createElement('ul');
            menu.setAttribute('id', 'context-menu');
            menu.appendChild(document.createElement('li'));
            menu.lastElementChild.appendChild(document.createTextNode('Export Subtree as JSON'));
            menu.lastElementChild.addEventListener('click', async() => {
                const response = await fetch(location.href  + '/node/' + e.target.parentElement.parentElement.getAttribute('data-uuid') + '/json');
                window.imm.displayExport(await response.text());
            });
            menu.appendChild(document.createElement('li'));
            menu.lastElementChild.appendChild(document.createTextNode('Export Subtree as XML'));
            menu.lastElementChild.addEventListener('click', async() => {
                const response = await fetch(location.href  + '/node/' + e.target.parentElement.parentElement.getAttribute('data-uuid') + '/xml');
                window.imm.displayExport(await response.text());
            });
            menu.appendChild(document.createElement('li'));
            menu.lastElementChild.appendChild(document.createTextNode('Delete Subtree'));
            menu.lastElementChild.addEventListener('click', () => {
                if (e.target.parentElement.parentElement === document.body) {
                    return;
                }
                if (! window.confirm('Do you really want to delete this sub tree?')) {
                    return;
                }
                e.target.parentElement.parentElement.removeChild(e.target.parentElement);
                fetch(location.href  + '/node/' + e.target.parentElement.getAttribute('data-uuid'), {
                    method: 'DELETE',
                });
            });
            menu.appendChild(document.createElement('li'));
            menu.lastElementChild.appendChild(document.createTextNode('Close'));
            menu.lastElementChild.addEventListener('click', () => document.body.removeChild(menu));
            menu.setAttribute('style', 'left: '+window.imm.mouseX+'px;top:'+window.imm.mouseY+'px');
            document.body.appendChild(menu);
        }
    },
    async getEditedValues(defaultText = '', defaultDescription = '') {
        return new Promise((resolve) => {
            document.getElementById('text').value = defaultText;
            document.getElementById('description').value = defaultDescription;
            const wrapper = document.getElementById('node-modification');
            wrapper.setAttribute('style', 'display:block');
            wrapper.getElementsByTagName('button')[0].onclick = () => {
                document.getElementById('node-modification').setAttribute('style', 'display: none');
                resolve({
                    text: defaultText,
                    description: defaultDescription,
                });
            }
            wrapper.getElementsByTagName('button')[1].onclick = () => {
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
    createContentSpan({text, description, uuid, image}) {
        const span = document.createElement('span');
        span.setAttribute('onclick', "window.imm.edit('" + uuid + "')");
        description || image ? span.classList.add('describes') : null;
        const info = document.createElement('strong');
        info.innerText = 'i';
        span.appendChild(info);
        const content = document.createElement('span');
        content.innerText = text;
        span.appendChild(content);
        const title = document.createElement('em');
        title.innerText = description ?? '';
        span.appendChild(title);
        const img = document.createElement('img');
        img.setAttribute('src', image ? `/images/${window.location.pathname.split('/')[1]}/${uuid}.${image}` : '');
        span.appendChild(img);
        return span;
    },
    createContentLi({text, description, uuid, image}) {
        const li = document.createElement('li');
        li.setAttribute('id', 'node-' + uuid);
        li.setAttribute('data-uuid', uuid);
        li.appendChild(window.imm.createContentSpan({text, description, uuid, image}));
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
                        el.firstElementChild.childNodes[3].setAttribute('src', node.image ? `/images/${window.location.pathname.split('/')[1]}/${node.uuid}.${node.image}` : '');
                    } else {
                        const parent = document.getElementById('node-'+node.parentUuid).lastElementChild;
                        parent.insertBefore(window.imm.createContentLi(node), parent.lastElementChild);
                    }
                }
            }
        }
        window.setTimeout(() => window.imm.drawArrows(window.imm.rootUuid), 1);
        window.setTimeout(window.imm.update, 1000);
    },
    async edit(nodeId) {
        const parent = document.getElementById('node-' + nodeId);
        const {text, description} = await window.imm.getEditedValues(parent.firstElementChild.childNodes[1].innerText, parent.firstElementChild.childNodes[2].innerText)
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
            window.setTimeout(() => window.imm.drawArrows(window.imm.rootUuid), 1);
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
        window.imm.rootUuid = window.imm.rootUuid ?? nodeId;
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
window.addEventListener('contextmenu', window.imm.context);
window.addEventListener('click', () => document.getElementById('context-menu')?.parentElement.removeChild(document.getElementById('context-menu')));
window.addEventListener("mousemove", window.imm.mouse);
window.setTimeout(window.imm.update, 1000);
