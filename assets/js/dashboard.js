let products=[], chartStock, chartCategory;
let modal = new bootstrap.Modal(document.getElementById('productModal'));
let viewModal = new bootstrap.Modal(document.getElementById('viewModal'));

function updateStats(){
    document.getElementById("totalProducts").innerText = products.length;
    let totalStock = products.reduce((a,b)=>a+parseInt(b.stock_quantity||0),0);
    let totalRevenue = products.reduce((a,b)=>a+(parseInt(b.stock_quantity||0)*parseFloat(b.price||0)),0);
    document.getElementById("totalStock").innerText = totalStock;
    document.getElementById("totalRevenue").innerText = `Rs ${totalRevenue.toFixed(2)}`;

    // Stock Chart
    if(chartStock) chartStock.destroy();
    let ctx = document.getElementById('stockChart').getContext('2d');
    chartStock = new Chart(ctx,{
        type:'bar',
        data:{labels:products.map(p=>p.name),datasets:[{label:'Stock',data:products.map(p=>p.stock_quantity),backgroundColor:'#4f46e5'}]},
        options:{plugins:{legend:{display:false}},responsive:true,maintainAspectRatio:false}
    });

    // Category Chart
    if(chartCategory) chartCategory.destroy();
    let categories={};
    products.forEach(p=>{if(p.category) categories[p.category]=(categories[p.category]||0)+1;});
    let ctxCat = document.getElementById('categoryChart').getContext('2d');
    chartCategory = new Chart(ctxCat,{
        type:'pie',
        data:{labels:Object.keys(categories),datasets:[{data:Object.values(categories),backgroundColor:['#4f46e5','#14b8a6','#f59e0b','#ef4444']}]},
        options:{plugins:{legend:{position:'bottom'}},responsive:true,maintainAspectRatio:false}
    });
}

async function loadProducts(){
    let res = await fetch("?action=read");
    let data = await res.json();
    products = data.data;
    let body=document.getElementById("productsBody");
    body.innerHTML="";
    products.forEach(p=>{
        body.innerHTML += `<tr>
        <td>${p.product_id}</td>
        <td><img src="${p.image_url||'https://via.placeholder.com/50'}" class="thumb"></td>
        <td>${p.name}</td>
        <td>${p.category?'<span class="badge-category">'+p.category+'</span>':''}</td>
        <td>Rs ${p.price}</td>
        <td>${p.stock_quantity}</td>
        <td>
        <button class="btn btn-sm btn-success" onclick="viewProduct(${p.product_id})">View</button>
        <button class="btn btn-sm btn-info" onclick="editProduct(${p.product_id})">Edit</button>
        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.product_id})">Delete</button>
        </td></tr>`;
    });
    updateStats();
}

document.getElementById("btnAdd").onclick=()=>{document.getElementById("productForm").reset();document.getElementById("product_id").value="";modal.show();};
document.getElementById("productForm").onsubmit=async e=>{e.preventDefault();let fd=new FormData(e.target);let action=fd.get("product_id")?"update":"create";let res=await fetch("?action="+action,{method:"POST",body:fd});let data=await res.json();if(data.success){modal.hide();loadProducts();}else alert("Save failed");};
function editProduct(id){let p=products.find(x=>x.product_id==id);if(!p)return;for(let k in p)if(document.getElementById(k))document.getElementById(k).value=p[k];modal.show();}
async function deleteProduct(id){if(!confirm("Delete product?"))return;let fd=new FormData();fd.append("product_id",id);let res=await fetch("?action=delete",{method:"POST",body:fd});let data=await res.json();if(data.success)loadProducts();}
function viewProduct(id){
    let p = products.find(x=>x.product_id == id);
    if(!p) return;
    document.getElementById("viewImage").src = p.image_url || "https://via.placeholder.com/400";
    document.getElementById("viewName").innerText = p.name;
    document.getElementById("viewDescription").innerText = p.description || "No description available.";
    document.getElementById("viewPrice").innerText = p.price;
    document.getElementById("viewStock").innerText = p.stock_quantity;
    document.getElementById("viewCategory").innerText = p.category || "Uncategorized";
    viewModal.show();
}
document.getElementById("btnTheme").onclick=()=>{let body=document.body;body.classList.toggle("dark");body.classList.toggle("light");document.querySelectorAll(".card").forEach(c=>{c.classList.toggle("dark");c.classList.toggle("light");});};

loadProducts();