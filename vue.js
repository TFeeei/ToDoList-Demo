new Vue({
    el: "#root",
    data: {
        todoList: [],

        totalNum: "",
        addAlertShow: false,
        editAlertShow: false,

        inputTitle: "",
        inputContent: "",
        currentClickIndex: "",

        newTitle: "",
        newContent: "",

        inputSearch: "",

        currentPage: 1,
        todoPerPage: 5,
        finalPage: "",

        apiUrl: './DatabaseHandler.php',

        deletedLine: "",
    },

    mounted() {
        this.getToDoList();
    },

    computed: {
        // todoListデータを1ページに表示する件数によって分割する
        paginatedTodo() {
            const start = (this.currentPage - 1) * this.todoPerPage;
            const end = start + this.todoPerPage;
            return this.todoList.slice(start, end)
        },

        // 合計ページ数を計算する
        totalPages() {
            return Math.ceil(this.todoList.length / this.todoPerPage);
        },
    },

    methods: {
        // データを渡す
        postData(url, data) {
            axios.post(url, data)
                .then(res => {
                    console.log(res.data);
                }).catch(err => {
                    console.log(err);
                }).then(() => {
                    this.getToDoList()
                })
        },
        // データを受け取る
        getToDoList() {
            axios.get(this.apiUrl + '?action=getData')
                .then((res) => {
                    this.todoList = res.data;
                })
                .catch(err => {
                    console.log(err);
                })
        },

        // Todoの新規作成
        addTodo() {
            this.addAlertShow = true
        },
        addSureClick() {
            if (this.newTitle && this.newContent) {
                let addData = {
                    title: this.newTitle,
                    content: this.newContent,
                }
                this.postData(this.apiUrl + '?action=insertData', addData)
                this.addAlertShow = false
            } else {
                alert("タイトルと内容を両方入力してください")
            }
            const newTotalPages = Math.ceil((this.todoList.length + 1) / this.todoPerPage) // 最後のページに移動する
            this.currentPage = newTotalPages
        },

        // Todoの編集
        editTodo(item, index) {
            this.editAlertShow = true
            this.inputTitle = item.title
            this.inputContent = item.content
            this.currentClickIndex = parseInt(this.paginatedTodo[index].ID)
        },
        // 編集を確認する
        sureClick() {
            if (this.inputTitle && this.inputContent) {
                let updateData = {
                    title: this.inputTitle,
                    content: this.inputContent,
                    id: this.currentClickIndex
                }
                this.postData(this.apiUrl + '?action=updateData', updateData)
                this.editAlertShow = false
            } else {
                alert("タイトルと内容を両方入力してください")
            }

        },
        // 編集をキャンセルする
        cancelClick() {
            this.addAlertShow = false
            this.editAlertShow = false
        },

        // Todoの削除
        deleteTodo(item, index) {
            this.currentClickIndex = parseInt(this.paginatedTodo[index].ID) // 現在操作しているtodo項目のIDを渡す
            this.deletedLine = index

            setTimeout(() => {
                this.postData(this.apiUrl + '?action=deleteData', {
                    id: this.currentClickIndex
                })
                this.deletedLine = "";
            }, 800);

            // 删掉最后一页第一行后，自动变前一页。
        },

        // タイトルでTodoを検索する
        searchToDo() {
            if (this.inputSearch) {
                this.currentPage = 1 // 検索したらcurrentPageも更新すること
                this.todoList = this.todoList.filter((item) => {
                    return item.title.includes(this.inputSearch)
                })
                // this.todoList.length > 0 ? null : alert("結果なし")
            } else {
                this.getToDoList()
            }
            // todoList得返回。。。
        },

        // 選択されたページに移動する
        setPage(pageNum) {
            this.currentPage = pageNum;
        },

    },

    filters: {
        // 日時データの処理
        dateFormate(value) {
            const date = new Date(value);
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            return year + "年" + month + "月" + day + "日";
        }
    }



})