<%
    local h    = require "luci.http"
    local io   = require "nixio"
    local flag = true
    local run  = true
    local fd   = nil

    -- 在这之前是不能有任何语句，声明语句除外
    h.setfilehandler(
        function(field, chunk, eof)
            if not field or not run then return end
            
            if flag then
                h.write("上传中")
                flag = false
            end

            -- 将上传的文件保存到根目录下
            local path = "/" .. field.file
            
            if not fd then
                fd = io.open(path, "w")
            end
            
            fd:write(chunk)
            
            if eof and fd then
                fd:close()
                fd = nil
                
                h.write("<br />上传完成")
            end
        end
    )

    -- 这块代码一定也要放在setfilehandler下面。
    if h.formvalue("act") == "update" then
        return
    end
%>
<form id="update" name="update" action="<%=REQUEST_URI%>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="act" value="update" />
    选择需要上传的文件：
    <input type="file" id="updatePackage" name="updatePackage" />
    <input type="submit" id="updateBtn" class="cbi-button cbi-button-apply" value="升级" />
</form>
