<div class="pos-tab-content">
    <div class="row">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>@lang('hr::lang.loginid') </label>
                    <input type="text" class="form-control" name="username" value="{{$employee->username}}" readonly>

                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.password') <span class="required">*</span></label>
                    <input type="password" class="form-control" name="password">
                </div>

                <div class="form-group">
                    <label>@lang('hr::lang.retype_password') <span class="required">*</span></label>
                    <input type="password" name="retype_password" class="form-control" >
                </div>

                <?php if($employee->termination){?>

                <?php if(!empty($login->id)):?>
                <div class="form-group">
                    <label>@lang('hr::lang.active_deactive') </label>
                    <label class="css-input switch switch-sm switch-success">
                        <input id="<?php echo str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encrypt->encode($login->id)) ?>" <?php echo $login->active == 1 ? 'checked':'' ?> type="checkbox" onclick='employeeActivation(this)'><span></span>
                    </label>
                </div>
                <?php endif ?>

                <?php } ?>


            </div>
        </div>

   </div>
</div>