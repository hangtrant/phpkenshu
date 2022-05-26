var loadFile = function(event) {
    var image = document.getElementById('output');
    image.src = URL.createObjectURL(event.target.files[0]);
};
document.getElementById("product-form").onkeypress = (e) => {
    if (Event.keyCode === 13) {
        // エンターキーが押されたときの動作
        if (Event.target.type != 'submit' && Event.target.type != 'textarea') {
            // submitボタン、テキストエリア以外の場合はイベントをキャンセル
            return false;
        }
    }
}