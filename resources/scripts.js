window.imm = {
    add(parentId) {

    },
    async load(nodeId) {
        const button = document.getElementById('load-' + nodeId);
        const parent = button.parentElement;
        parent.classList.remove('unloaded');
        parent.classList.add('loading');
        parent.removeChild(button);
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
                    span.setAttribute('title', node.description);
                    li.appendChild(span);
                    const button = document.createElement('button');
                    button.innerText = 'load';
                    button.setAttribute('id', 'load-' + node.uuid);
                    button.setAttribute('type', 'button');
                    button.setAttribute('onclick', "window.imm.load('" + node.uuid +"')");
                    li.appendChild(button);
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
            }
        }
    }
};
